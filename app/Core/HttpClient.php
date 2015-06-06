<?php

namespace Core;

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
     * Send a POST HTTP request
     *
     * @access public
     * @param  string  $url
     * @param  array   $data
     * @param  array   $headers
     * @return string
     */
    public function post($url, array $data, array $headers = array())
    {
        if (empty($url)) {
            return '';
        }

        $headers = array_merge(array(
            'User-Agent: '.self::HTTP_USER_AGENT,
            'Content-Type: application/json',
            'Connection: close',
        ), $headers);

        $context = stream_context_create(array(
            'http' => array(
                'method' => 'POST',
                'protocol_version' => 1.1,
                'timeout' => self::HTTP_TIMEOUT,
                'max_redirects' => self::HTTP_MAX_REDIRECTS,
                'header' => implode("\r\n", $headers),
                'content' => json_encode($data)
            )
        ));

        $stream = @fopen(trim($url), 'r', false, $context);
        $response = '';

        if (is_resource($stream)) {
            $response = stream_get_contents($stream);
        }
        else {
            $this->container['logger']->error('HttpClient: request failed');
        }

        if (DEBUG) {
            $this->container['logger']->debug('HttpClient: url='.$url);
            $this->container['logger']->debug('HttpClient: payload='.var_export($data, true));
            $this->container['logger']->debug('HttpClient: metadata='.var_export(@stream_get_meta_data($stream), true));
            $this->container['logger']->debug('HttpClient: response='.$response);
        }

        return $response;
    }
}
