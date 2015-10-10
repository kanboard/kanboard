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
     * @param  array    $values   Event payload
     */
    public function notify(array $values)
    {
        $url = $this->config->get('webhook_url');
        $token = $this->config->get('webhook_token');

        if (! empty($url)) {

            if (strpos($url, '?') !== false) {
                $url .= '&token='.$token;
            }
            else {
                $url .= '?token='.$token;
            }

            return $this->httpClient->postJson($url, $values);
        }
    }
}
