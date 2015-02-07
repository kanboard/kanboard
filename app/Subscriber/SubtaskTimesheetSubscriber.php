<?php

namespace Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Model\SubTask;
use Event\SubtaskEvent;

class SubtaskTimesheetSubscriber extends Base implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            SubTask::EVENT_UPDATE => array('log', 0),
        );
    }

    public function log(SubtaskEvent $event)
    {
        if (isset($event['status'])) {

            $subtask = $this->subTask->getById($event['id']);

            if ($subtask['status'] == SubTask::STATUS_INPROGRESS) {
                $this->subtaskTimeTracking->logStartTime($subtask['id'], $subtask['user_id']);
            }
            else {
                $this->subtaskTimeTracking->logEndTime($subtask['id'], $subtask['user_id']);
            }
        }
    }
}
