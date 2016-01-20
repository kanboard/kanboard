<?php

namespace Kanboard\Subscriber;

use Kanboard\Event\TaskEvent;
use Kanboard\Model\Task;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RecurringTaskSubscriber extends BaseSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            Task::EVENT_MOVE_COLUMN => 'onMove',
            Task::EVENT_CLOSE => 'onClose',
        );
    }

    public function onMove(TaskEvent $event)
    {
        $this->logger->debug('Subscriber executed: '.__METHOD__);

        if ($event['recurrence_status'] == Task::RECURRING_STATUS_PENDING) {
            if ($event['recurrence_trigger'] == Task::RECURRING_TRIGGER_FIRST_COLUMN && $this->board->getFirstColumn($event['project_id']) == $event['src_column_id']) {
                $this->taskDuplication->duplicateRecurringTask($event['task_id']);
            } elseif ($event['recurrence_trigger'] == Task::RECURRING_TRIGGER_LAST_COLUMN && $this->board->getLastColumn($event['project_id']) == $event['dst_column_id']) {
                $this->taskDuplication->duplicateRecurringTask($event['task_id']);
            }
        }
    }

    public function onClose(TaskEvent $event)
    {
        $this->logger->debug('Subscriber executed: '.__METHOD__);

        if ($event['recurrence_status'] == Task::RECURRING_STATUS_PENDING && $event['recurrence_trigger'] == Task::RECURRING_TRIGGER_CLOSE) {
            $this->taskDuplication->duplicateRecurringTask($event['task_id']);
        }
    }
}
