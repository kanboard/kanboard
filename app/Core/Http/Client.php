<?php

namespace Kanboard\Core\Http;

use Kanboard\Core\Base;
use Kanboard\Job\HttpAsyncJob;

/**
 * HTTP client
 *
 * @package  http
 * @author   Frederic Guillot
 */
class Client extends Base
{
    /**
     * HTTP connection timeout in seconds
     *
     * @var integer
     */
    const HTTP_TIMEOUT = 5;

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
     * @return string
     */
    public function get($url, array $headers = array())
    {
        return $this->doRequest('GET', $url, '', $headers);
    }

    /**
     * Send a GET HTTP request and parse JSON response
     *
     * @access public
     * @param  string     $url
     * @param  string[]   $headers
     * @return array
     */
    public function getJson($url, array $headers = array())
    {
        $response = $this->doRequest('GET', $url, '', array_merge(array('Accept: application/json'), $headers));
        return json_decode($response, true) ?: array();
    }

    /**
     * Send a POST HTTP request encoded in JSON
     *
     * @access public
     * @param  string     $url
     * @param  array      $data
     * @param  string[]   $headers
     * @return string
     */
    public function postJson($url, array $data, array $headers = array())
    {
        return $this->doRequest(
            'POST',
            $url,
            json_encode($data),
            array_merge(array('Content-type: application/json'), $headers)
        );
    }

    /**
     * Send a POST HTTP request encoded in JSON (Fire and forget)
     *
     * @access public
     * @param  string     $url
     * @param  array      $data
     * @param  string[]   $headers
     */
    public function postJsonAsync($url, array $data, array $headers = array())
    {
        $this->queueManager->push(HttpAsyncJob::getInstance($this->container)->withParams(
            'POST',
            $url,
            json_encode($data),
            array_merge(array('Content-type: application/json'), $headers)
        ));
    }

    /**
     * Send a POST HTTP request encoded in www-form-urlencoded
     *
     * @access public
     * @param  string     $url
     * @param  array      $data
     * @param  string[]   $headers
     * @return string
     */
    public function postForm($url, array $data, array $headers = array())
    {
        return $this->doRequest(
            'POST',
            $url,
            http_build_query($data),
            array_merge(array('Content-type: application/x-www-form-urlencoded'), $headers)
        );
    }

    /**
     * Send a POST HTTP request encoded in www-form-urlencoded (fire and forget)
     *
     * @access public
     * @param  string     $url
     * @param  array      $data
     * @param  string[]   $headers
     */
    public function postFormAsync($url, array $data, array $headers = array())
    {
        $this->queueManager->push(HttpAsyncJob::getInstance($this->container)->withParams(
            'POST',
            $url,
            http_build_query($data),
            array_merge(array('Content-type: application/x-www-form-urlencoded'), $headers)
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
     * @return string
     */
    public function doRequest($method, $url, $content, array $headers)
    {
        if (empty($url)) {
            return '';
        }

        $startTime = microtime(true);
        $stream = @fopen(trim($url), 'r', false, stream_context_create($this->getContext($method, $content, $headers)));
        $response = '';

        if (is_resource($stream)) {
            $response = stream_get_contents($stream);
        } else {
            $this->logger->error('HttpClient: request failed');
        }

        if (DEBUG) {
            $this->logger->debug('HttpClient: url='.$url);
            $this->logger->debug('HttpClient: headers='.var_export($headers, true));
            $this->logger->debug('HttpClient: payload='.$content);
            $this->logger->debug('HttpClient: metadata='.var_export(@stream_get_meta_data($stream), true));
            $this->logger->debug('HttpClient: response='.$response);
            $this->logger->debug('HttpClient: executionTime='.(microtime(true) - $startTime));
        }

        return $response;
    }

    /**
     * Get stream context
     *
     * @access private
     * @param  string     $method
     * @param  string     $content
     * @param  string[]   $headers
     * @return array
     */
    private function getContext($method, $content, array $headers)
    {
        $default_headers = array(
            'User-Agent: '.self::HTTP_USER_AGENT,
            'Connection: close',
        );

        if (HTTP_PROXY_USERNAME) {
            $default_headers[] = 'Proxy-Authorization: Basic '.base64_encode(HTTP_PROXY_USERNAME.':'.HTTP_PROXY_PASSWORD);
        }

        $headers = array_merge($default_headers, $headers);

        $context = array(
            'http' => array(
                'method' => $method,
                'protocol_version' => 1.1,
                'timeout' => self::HTTP_TIMEOUT,
                'max_redirects' => self::HTTP_MAX_REDIRECTS,
                'header' => implode("\r\n", $headers),
                'content' => $content,
            )
        );

        if (HTTP_PROXY_HOSTNAME) {
            $context['http']['proxy'] = 'tcp://'.HTTP_PROXY_HOSTNAME.':'.HTTP_PROXY_PORT;
            $context['http']['request_fulluri'] = true;
        }

        if (HTTP_VERIFY_SSL_CERTIFICATE === false) {
            $context['ssl'] = array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            );
        }

        return $context;
    }
}
