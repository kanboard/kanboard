<?php

namespace Core;

use Pimple\Container;

/**
 * HTTP client
 *
 * @package  core
 * @author   Frederic Guillot
 */
class HttpClient
{
    /**
     * HTTP connection timeout in seconds
     *
     * @var integer
     */
    const HTTP_TIMEOUT = 2;

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
    const HTTP_USER_AGENT = 'Kanboard Webhook';

    /**
     * Container instance
     *
     * @access protected
     * @var \Pimple\Container
     */
    protected $container;

    /**
     * Constructor
     *
     * @access public
     * @param  \Pimple\Container   $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Send a POST HTTP request
     *
     * @access public
     * @param  string  $url
     * @param  array   $data
     * @return string
     */
    public function post($url, array $data)
    {
        if (empty($url)) {
            return '';
        }

        $headers = array(
            'User-Agent: '.self::HTTP_USER_AGENT,
            'Content-Type: application/json',
            'Connection: close',
        );

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

        $response = @file_get_contents(trim($url), false, $context);

        if (DEBUG) {
            $this->container['logger']->debug($url);
            $this->container['logger']->debug(var_export($data, true));
            $this->container['logger']->debug($response);
        }

        return $response;
    }
}
