<?php

namespace Kanboard\Core;

/**
 * HTTP client
 *
 * @package  core
 * @author   Frederic Guillot
 */
class HttpClient extends Base
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
     * Make the HTTP request
     *
     * @access private
     * @param  string     $method
     * @param  string     $url
     * @param  string     $content
     * @param  string[]   $headers
     * @return string
     */
    private function doRequest($method, $url, $content, array $headers)
    {
        if (empty($url)) {
            return '';
        }

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
                'content' => $content
            )
        );

        if (HTTP_PROXY_HOSTNAME) {
            $context['http']['proxy'] = 'tcp://'.HTTP_PROXY_HOSTNAME.':'.HTTP_PROXY_PORT;
            $context['http']['request_fulluri'] = true;
        }

        $stream = @fopen(trim($url), 'r', false, stream_context_create($context));
        $response = '';

        if (is_resource($stream)) {
            $response = stream_get_contents($stream);
        } else {
            $this->container['logger']->error('HttpClient: request failed');
        }

        if (DEBUG) {
            $this->container['logger']->debug('HttpClient: url='.$url);
            $this->container['logger']->debug('HttpClient: payload='.$content);
            $this->container['logger']->debug('HttpClient: metadata='.var_export(@stream_get_meta_data($stream), true));
            $this->container['logger']->debug('HttpClient: response='.$response);
        }

        return $response;
    }
}
