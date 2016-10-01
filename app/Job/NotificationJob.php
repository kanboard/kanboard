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
     * @return $this
     */
    public function withParams(GenericEvent $event, $eventName)
    {
        $this->jobParams = array($event->getAll(), $eventName);
        return $this;
    }

    /**
     * Execute job
     *
     * @param array  $eventData
     * @param string $eventName
     */
    public function execute(array $eventData, $eventName)
    {
        if (! empty($eventData['mention'])) {
            $this->userNotificationModel->sendUserNotification($eventData['mention'], $eventName, $eventData);
        } else {
            $this->userNotificationModel->sendNotifications($eventName, $eventData);
            $this->projectNotificationModel->sendNotifications($eventData['task']['project_id'], $eventName, $eventData);
        }
    }
}
