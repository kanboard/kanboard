<?php

namespace Subscriber;

use Event\TaskEvent;
use Model\Task;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RecurringTaskSubscriber extends Base implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            Task::EVENT_MOVE_COLUMN => array('onMove', 0),
            Task::EVENT_CLOSE => array('onClose', 0),
        );
    }

    public function onMove(TaskEvent $event)
    {
        if ($event['recurrence_status'] == Task::RECURE_STATUS_PENDING) {

            if ($event['recurrence_trigger'] == Task::RECURE_TRIGGER_FIRST && $this->board->getFirstColumn($event['project_id']) == $event['src_column_id']) {
                $this->taskDuplication->duplicateRecurringTask($event['task_id']);
            }
            else if ($event['recurrence_trigger'] == Task::RECURE_TRIGGER_LAST && $this->board->getLastColumn($event['project_id']) == $event['dst_column_id']) {
                $this->taskDuplication->duplicateRecurringTask($event['task_id']);
            }
        }
    }

    public function onClose(TaskEvent $event)
    {
        if ($event['recurrence_status'] == Task::RECURE_STATUS_PENDING && $event['recurrence_trigger'] == Task::RECURE_TRIGGER_CLOSE) {
            $this->taskDuplication->duplicateRecurringTask($event['task_id']);
        }
    }
}
