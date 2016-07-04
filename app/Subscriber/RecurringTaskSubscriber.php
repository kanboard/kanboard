<?php

namespace Kanboard\Subscriber;

use Kanboard\Event\TaskEvent;
use Kanboard\Model\TaskModel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RecurringTaskSubscriber extends BaseSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            TaskModel::EVENT_MOVE_COLUMN => 'onMove',
            TaskModel::EVENT_CLOSE       => 'onClose',
        );
    }

    public function onMove(TaskEvent $event)
    {
        $this->logger->debug('Subscriber executed: '.__METHOD__);

        if ($event['recurrence_status'] == TaskModel::RECURRING_STATUS_PENDING) {
            if ($event['recurrence_trigger'] == TaskModel::RECURRING_TRIGGER_FIRST_COLUMN && $this->columnModel->getFirstColumnId($event['project_id']) == $event['src_column_id']) {
                $this->taskRecurrenceModel->duplicateRecurringTask($event['task_id']);
            } elseif ($event['recurrence_trigger'] == TaskModel::RECURRING_TRIGGER_LAST_COLUMN && $this->columnModel->getLastColumnId($event['project_id']) == $event['dst_column_id']) {
                $this->taskRecurrenceModel->duplicateRecurringTask($event['task_id']);
            }
        }
    }

    public function onClose(TaskEvent $event)
    {
        $this->logger->debug('Subscriber executed: '.__METHOD__);

        if ($event['recurrence_status'] == TaskModel::RECURRING_STATUS_PENDING && $event['recurrence_trigger'] == TaskModel::RECURRING_TRIGGER_CLOSE) {
            $this->taskRecurrenceModel->duplicateRecurringTask($event['task_id']);
        }
    }
}
