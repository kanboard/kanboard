<?php

namespace Kanboard\Job;

use Kanboard\Event\GenericEvent;

/**
 * Class NotificationJob
 *
 * @package Kanboard\Job
 * @author  Frederic Guillot
 */
class NotificationJob extends BaseJob
{
    /**
     * Set job parameters
     *
     * @param GenericEvent $event
     * @param string       $eventName
     * @param string       $eventObjectName
     * @return $this
     */
    public function withParams(GenericEvent $event, $eventName, $eventObjectName)
    {
        $this->jobParams = array($event->getAll(), $eventName, $eventObjectName);
        return $this;
    }

    /**
     * Execute job
     *
     * @param array  $event
     * @param string $eventName
     * @param string $eventObjectName
     */
    public function execute(array $event, $eventName, $eventObjectName)
    {
        $eventData = $this->getEventData($event, $eventObjectName);

        if (! empty($eventData)) {
            if (! empty($event['mention'])) {
                $this->userNotification->sendUserNotification($event['mention'], $eventName, $eventData);
            } else {
                $this->userNotification->sendNotifications($eventName, $eventData);
                $this->projectNotification->sendNotifications($eventData['task']['project_id'], $eventName, $eventData);
            }
        }
    }

    /**
     * Get event data
     *
     * @param array  $event
     * @param string $eventObjectName
     * @return array
     */
    public function getEventData(array $event, $eventObjectName)
    {
        $values = array();

        if (! empty($event['changes'])) {
            $values['changes'] = $event['changes'];
        }

        switch ($eventObjectName) {
            case 'Kanboard\Event\TaskEvent':
                $values['task'] = $this->taskFinder->getDetails($event['task_id']);
                break;
            case 'Kanboard\Event\SubtaskEvent':
                $values['subtask'] = $this->subtask->getById($event['id'], true);
                $values['task'] = $this->taskFinder->getDetails($values['subtask']['task_id']);
                break;
            case 'Kanboard\Event\FileEvent':
                $values['file'] = $event;
                $values['task'] = $this->taskFinder->getDetails($values['file']['task_id']);
                break;
            case 'Kanboard\Event\CommentEvent':
                $values['comment'] = $this->comment->getById($event['id']);
                $values['task'] = $this->taskFinder->getDetails($values['comment']['task_id']);
                break;
        }

        return $values;
    }
}
