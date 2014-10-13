<?php

namespace Event;

/**
 * Webhook task events
 *
 * @package event
 * @author  Frederic Guillot
 */
class WebhookListener extends Base
{
    /**
     * Url to call
     *
     * @access private
     * @var string
     */
    private $url = '';

    /**
     * Set webhook url
     *
     * @access public
     * @param  string     $url       URL to call
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Execute the action
     *
     * @access public
     * @param  array   $data   Event data dictionary
     * @return bool            True if the action was executed or false when not executed
     */
    public function execute(array $data)
    {
        $this->webhook->notify($this->url, $data);
        return true;
    }
}
