<?php

namespace Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Model\Subtask;
use Event\SubtaskEvent;

class SubtaskTimesheetSubscriber extends Base implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            Subtask::EVENT_CREATE => array('updateTaskTime', 0),
            Subtask::EVENT_UPDATE => array(
                array('logStartEnd', 10),
                array('updateTaskTime', 0),
            )
        );
    }

    public function updateTaskTime(SubtaskEvent $event)
    {
        if (isset($event['task_id'])) {
            $this->subtaskTimeTracking->updateTaskTimeTracking($event['task_id']);
        }
    }

    public function logStartEnd(SubtaskEvent $event)
    {
        if ($this->config->get('subtask_time_tracking') == 1 && isset($event['status'])) {

            $subtask = $this->subtask->getById($event['id']);

            if (empty($subtask['user_id'])) {
                return false;
            }

            if ($subtask['status'] == Subtask::STATUS_INPROGRESS) {
                return $this->subtaskTimeTracking->logStartTime($subtask['id'], $subtask['user_id']);
            }
            else {
                return $this->subtaskTimeTracking->logEndTime($subtask['id'], $subtask['user_id']);
            }
        }
    }
}
