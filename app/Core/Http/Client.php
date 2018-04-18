<?php

namespace Kanboard\Core\Http;

use Kanboard\Core\Base;
use Kanboard\Job\HttpAsyncJob;

/**
 * HTTP client
 *
 * @package  Kanboard\Core\Http
 * @author   Frederic Guillot
 */
class Client extends Base
{
    /**
     * HTTP connection timeout in seconds
     *
     * @var integer
     */
    const HTTP_TIMEOUT = 10;

    /**
     * Number of maximum redirections for the HTTP client
     *
     * @var integer
     */
    const HTTP_MAX_REDIRECTS = 2;

    /**
     * HTTP client user agent
     *
     * @var string
     */
    const HTTP_USER_AGENT = 'Kanboard';

    /**
     * Send a GET HTTP request
     *
     * @access public
     * @param  string     $url
     * @param  string[]   $headers
     * @param  bool       $raiseForErrors
     * @return string
     */
    public function get($url, array $headers = [], $raiseForErrors = false)
    {
        return $this->doRequest('GET', $url, '', $headers, $raiseForErrors);
    }

    /**
     * Send a GET HTTP request and parse JSON response
     *
     * @access public
     * @param  string     $url
     * @param  string[]   $headers
     * @param  bool       $raiseForErrors
     * @return array
     */
    public function getJson($url, array $headers = [], $raiseForErrors = false)
    {
        $response = $this->doRequest('GET', $url, '', array_merge(['Accept: application/json'], $headers), $raiseForErrors);
        return json_decode($response, true) ?: [];
    }

    /**
     * Send a POST HTTP request encoded in JSON
     *
     * @access public
     * @param  string     $url
     * @param  array      $data
     * @param  string[]   $headers
     * @param  bool       $raiseForErrors
     * @return string
     */
    public function postJson($url, array $data, array $headers = [], $raiseForErrors = false)
    {
        return $this->doRequest(
            'POST',
            $url,
            json_encode($data),
            array_merge(['Content-type: application/json'], $headers),
            $raiseForErrors
        );
    }

    /**
     * Send a POST HTTP request encoded in JSON (Fire and forget)
     *
     * @access public
     * @param  string     $url
     * @param  array      $data
     * @param  string[]   $headers
     * @param  bool       $raiseForErrors
     */
    public function postJsonAsync($url, array $data, array $headers = [], $raiseForErrors = false)
    {
        $this->queueManager->push(HttpAsyncJob::getInstance($this->container)->withParams(
            'POST',
            $url,
            json_encode($data),
            array_merge(['Content-type: application/json'], $headers),
            $raiseForErrors
        ));
    }

    /**
     * Send a POST HTTP request encoded in www-form-urlencoded
     *
     * @access public
     * @param  string     $url
     * @param  array      $data
     * @param  string[]   $headers
     * @param  bool       $raiseForErrors
     * @return string
     */
    public function postForm($url, array $data, array $headers = [], $raiseForErrors = false)
    {
        return $this->doRequest(
            'POST',
            $url,
            http_build_query($data),
            array_merge(['Content-type: application/x-www-form-urlencoded'], $headers),
            $raiseForErrors
        );
    }

    /**
     * Send a POST HTTP request encoded in www-form-urlencoded (fire and forget)
     *
     * @access public
     * @param  string     $url
     * @param  array      $data
     * @param  string[]   $headers
     * @param  bool       $raiseForErrors
     */
    public function postFormAsync($url, array $data, array $headers = [], $raiseForErrors = false)
    {
        $this->queueManager->push(HttpAsyncJob::getInstance($this->container)->withParams(
            'POST',
            $url,
            http_build_query($data),
            array_merge(['Content-type: application/x-www-form-urlencoded'], $headers),
            $raiseForErrors
        ));
    }

    /**
     * Make the HTTP request
     *
     * @access public
     * @param  string     $method
     * @param  string     $url
     * @param  string     $content
     * @param  string[]   $headers
     * @param  bool       $raiseForErrors
     * @return string
     */
    public function doRequest($method, $url, $content, array $headers, $raiseForErrors = false)
    {
        if (empty($url)) {
            return '';
        }

        $startTime = microtime(true);
        $stream = @fopen(trim($url), 'r', false, stream_context_create($this->getContext($method, $content, $headers, $raiseForErrors)));

        if (! is_resource($stream)) {
            $this->logger->error('HttpClient: request failed ('.$url.')');

            if ($raiseForErrors) {
                throw new ClientException('Unreachable URL: '.$url);
            }

            return '';
        }

        $body = stream_get_contents($stream);
        $metadata = stream_get_meta_data($stream);

        if ($raiseForErrors && array_key_exists('wrapper_data', $metadata)) {
            $statusCode = $this->getStatusCode($metadata['wrapper_data']);

            if ($statusCode >= 400) {
                throw new InvalidStatusException('Request failed with status code '.$statusCode, $statusCode, $body);
            }
        }

        if (DEBUG) {
            $this->logger->debug('HttpClient: url='.$url);
            $this->logger->debug('HttpClient: headers='.var_export($headers, true));
            $this->logger->debug('HttpClient: payload='.$content);
            $this->logger->debug('HttpClient: metadata='.var_export($metadata, true));
            $this->logger->debug('HttpClient: body='.$body);
            $this->logger->debug('HttpClient: executionTime='.(microtime(true) - $startTime));
        }

        return $body;
    }

    /**
     * Get stream context
     *
     * @access private
     * @param  string     $method
     * @param  string     $content
     * @param  string[]   $headers
     * @param  bool       $raiseForErrors
     * @return array
     */
    private function getContext($method, $content, array $headers, $raiseForErrors = false)
    {
        $default_headers = [
            'User-Agent: '.self::HTTP_USER_AGENT,
            'Connection: close',
        ];

        if (HTTP_PROXY_USERNAME) {
            $default_headers[] = 'Proxy-Authorization: Basic '.base64_encode(HTTP_PROXY_USERNAME.':'.HTTP_PROXY_PASSWORD);
        }

        $headers = array_merge($default_headers, $headers);

        $context = [
            'http' => [
                'method' => $method,
                'protocol_version' => 1.1,
                'timeout' => self::HTTP_TIMEOUT,
                'max_redirects' => self::HTTP_MAX_REDIRECTS,
                'header' => implode("\r\n", $headers),
                'content' => $content,
                'ignore_errors' => $raiseForErrors,
            ]
        ];

        if (HTTP_PROXY_HOSTNAME) {
            $context['http']['proxy'] = 'tcp://'.HTTP_PROXY_HOSTNAME.':'.HTTP_PROXY_PORT;
            $context['http']['request_fulluri'] = true;
        }

        if (HTTP_VERIFY_SSL_CERTIFICATE === false) {
            $context['ssl'] = [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ];
        }

        return $context;
    }

    private function getStatusCode(array $lines)
    {
        $status = 200;

        foreach ($lines as $line) {
            if (strpos($line, 'HTTP/1') === 0) {
                $status = (int) substr($line, 9, 3);
            }
        }

        return $status;
    }
}
