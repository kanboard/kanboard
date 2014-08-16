<?php

namespace Event;

use Core\Listener;
use Model\Notification;

/**
 * Base notification listener
 *
 * @package event
 * @author  Frederic Guillot
 */
abstract class BaseNotificationListener implements Listener
{
    /**
     * Notification model
     *
     * @accesss protected
     * @var Model\Notification
     */
    protected $notification;

    /**
     * Template name
     *
     * @accesss private
     * @var string
     */
    private $template = '';

    /**
     * Fetch data for the mail template
     *
     * @access public
     * @param  array    $data    Event data
     * @return array
     */
    abstract public function getTemplateData(array $data);

    /**
     * Constructor
     *
     * @access public
     * @param  \Model\Notification   $notification   Notification model instance
     * @param  string                $template       Template name
     */
    public function __construct(Notification $notification, $template)
    {
        $this->template = $template;
        $this->notification = $notification;
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
        $values = $this->getTemplateData($data);

        // Get the list of users to be notified
        $users = $this->notification->getUsersList($values['task']['project_id']);

        // Send notifications
        if ($users) {
            $this->notification->sendEmails($this->template, $users, $values);
            return true;
        }

        return false;
    }
}
