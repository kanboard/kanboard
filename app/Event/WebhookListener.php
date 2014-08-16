<?php

namespace Event;

use Core\Listener;
use Model\Webhook;

/**
 * Webhook task events
 *
 * @package event
 * @author  Frederic Guillot
 */
class WebhookListener implements Listener
{
    /**
     * Webhook model
     *
     * @accesss private
     * @var \Model\Webhook
     */
    private $webhook;

    /**
     * Url to call
     *
     * @access private
     * @var string
     */
    private $url = '';

    /**
     * Constructor
     *
     * @access public
     * @param  string           $url       URL to call
     * @param  \Model\Webhook   $webhook   Webhook model instance
     */
    public function __construct($url, Webhook $webhook)
    {
        $this->url = $url;
        $this->webhook = $webhook;
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
