<?php

namespace Model;

/**
 * Webhook model
 *
 * @package  model
 * @author   Frederic Guillot
 */
class Webhook extends Base
{
    /**
     * HTTP connection timeout in seconds
     *
     * @var integer
     */
    const HTTP_TIMEOUT = 1;

    /**
     * Number of maximum redirections for the HTTP client
     *
     * @var integer
     */
    const HTTP_MAX_REDIRECTS = 3;

    /**
     * HTTP client user agent
     *
     * @var string
     */
    const HTTP_USER_AGENT = 'Kanboard Webhook';

    /**
     * Call the external URL
     *
     * @access public
     * @param  string   $url    URL to call
     * @param  array    $task   Task data
     */
    public function notify($url, array $task)
    {
        $token = $this->config->get('webhook_token');

        $headers = array(
            'Connection: close',
            'User-Agent: '.self::HTTP_USER_AGENT,
        );

        $context = stream_context_create(array(
            'http' => array(
                'method' => 'POST',
                'protocol_version' => 1.1,
                'timeout' => self::HTTP_TIMEOUT,
                'max_redirects' => self::HTTP_MAX_REDIRECTS,
                'header' => implode("\r\n", $headers),
                'content' => json_encode($task)
            )
        ));

        if (strpos($url, '?') !== false) {
            $url .= '&token='.$token;
        }
        else {
            $url .= '?token='.$token;
        }

        @file_get_contents($url, false, $context);
    }
}
