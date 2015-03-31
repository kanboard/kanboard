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
     * Call the external URL
     *
     * @access public
     * @param  string   $url    URL to call
     * @param  array    $task   Task data
     */
    public function notify($url, array $task)
    {
        $token = $this->config->get('webhook_token');

        if (strpos($url, '?') !== false) {
            $url .= '&token='.$token;
        }
        else {
            $url .= '?token='.$token;
        }

        return $this->httpClient->post($url, $task);
    }
}
