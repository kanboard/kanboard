<?php

namespace Kanboard\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Kanboard\Model\Subtask;
use Kanboard\Event\SubtaskEvent;

class SubtaskTimeTrackingSubscriber extends BaseSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            Subtask::EVENT_CREATE => 'updateTaskTime',
            Subtask::EVENT_DELETE => 'updateTaskTime',
            Subtask::EVENT_UPDATE => array(
                array('logStartEnd', 10),
                array('updateTaskTime', 0),
            )
        );
    }

    public function updateTaskTime(SubtaskEvent $event)
    {
        if (isset($event['task_id'])) {
            $this->logger->debug('Subscriber executed: '.__METHOD__);
            $this->subtaskTimeTracking->updateTaskTimeTracking($event['task_id']);
        }
    }

    public function logStartEnd(SubtaskEvent $event)
    {
        if (isset($event['status']) && $this->config->get('subtask_time_tracking') == 1) {
            $this->logger->debug('Subscriber executed: '.__METHOD__);
            $subtask = $this->subtask->getById($event['id']);

            if (empty($subtask['user_id'])) {
                return false;
            }

            if ($subtask['status'] == Subtask::STATUS_INPROGRESS) {
                return $this->subtaskTimeTracking->logStartTime($subtask['id'], $subtask['user_id']);
            } else {
                return $this->subtaskTimeTracking->logEndTime($subtask['id'], $subtask['user_id']);
            }
        }
    }
}
