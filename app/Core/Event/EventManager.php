<?php

namespace Kanboard\Core\Event;

use Kanboard\Model\Task;
use Kanboard\Model\TaskLink;

/**
 * Event Manager
 *
 * @package  event
 * @author   Frederic Guillot
 */
class EventManager
{
    /**
     * Extended events
     *
     * @access private
     * @var array
     */
    private $events = array();

    /**
     * Add new event
     *
     * @access public
     * @param  string  $event
     * @param  string  $description
     * @return EventManager
     */
    public function register($event, $description)
    {
        $this->events[$event] = $description;
        return $this;
    }

    /**
     * Get the list of events and description that can be used from the user interface
     *
     * @access public
     * @return array
     */
    public function getAll()
    {
        $events = array(
            TaskLink::EVENT_CREATE_UPDATE => t('Task link creation or modification'),
            Task::EVENT_MOVE_COLUMN => t('Move a task to another column'),
            Task::EVENT_UPDATE => t('Task modification'),
            Task::EVENT_CREATE => t('Task creation'),
            Task::EVENT_OPEN => t('Reopen a task'),
            Task::EVENT_CLOSE => t('Closing a task'),
            Task::EVENT_CREATE_UPDATE => t('Task creation or modification'),
            Task::EVENT_ASSIGNEE_CHANGE => t('Task assignee change'),
        );

        $events = array_merge($events, $this->events);
        asort($events);

        return $events;
    }
}
