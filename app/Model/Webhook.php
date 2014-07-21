<?php

namespace Model;

use Event\WebhookListener;

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
     * URL to call for task creation
     *
     * @access private
     * @var string
     */
    private $url_task_creation = '';

    /**
     * URL to call for task modification
     *
     * @access private
     * @var string
     */
    private $url_task_modification = '';

    /**
     * Webook token
     *
     * @access private
     * @var string
     */
    private $token = '';

    /**
     * Attach events
     *
     * @access public
     */
    public function attachEvents()
    {
        $config = new Config($this->db, $this->event);

        $this->url_task_creation = $config->get('webhooks_url_task_creation');
        $this->url_task_modification = $config->get('webhooks_url_task_modification');
        $this->token = $config->get('webhooks_token');

        if ($this->url_task_creation) {
            $this->attachCreateEvents();
        }

        if ($this->url_task_modification) {
            $this->attachUpdateEvents();
        }
    }

    /**
     * Attach events for task modification
     *
     * @access public
     */
    public function attachUpdateEvents()
    {
        $events = array(
            Task::EVENT_UPDATE,
            Task::EVENT_CLOSE,
            Task::EVENT_OPEN,
        );

        $listener = new WebhookListener($this->url_task_modification, $this);

        foreach ($events as $event_name) {
            $this->event->attach($event_name, $listener);
        }
    }

    /**
     * Attach events for task creation
     *
     * @access public
     */
    public function attachCreateEvents()
    {
        $events = array(
            Task::EVENT_CREATE,
        );

        $listener = new WebhookListener($this->url_task_creation, $this);

        foreach ($events as $event_name) {
            $this->event->attach($event_name, $listener);
        }
    }

    /**
     * Call the external URL
     *
     * @access public
     * @param  string   $url    URL to call
     * @param  array    $task   Task data
     */
    public function notify($url, array $task)
    {
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
            $url .= '&token='.$this->token;
        }
        else {
            $url .= '?token='.$this->token;
        }

        @file_get_contents($url, false, $context);
    }
}
