<?php

namespace Kanboard\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Kanboard\Model\SubtaskModel;
use Kanboard\Event\SubtaskEvent;

class SubtaskTimeTrackingSubscriber extends BaseSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            SubtaskModel::EVENT_CREATE => 'updateTaskTime',
            SubtaskModel::EVENT_DELETE => 'updateTaskTime',
            SubtaskModel::EVENT_UPDATE => array(
                array('logStartEnd', 10),
                array('updateTaskTime', 0),
            )
        );
    }

    public function updateTaskTime(SubtaskEvent $event)
    {
        if (isset($event['task_id'])) {
            $this->logger->debug('Subscriber executed: '.__METHOD__);
            $this->subtaskTimeTrackingModel->updateTaskTimeTracking($event['task_id']);
        }
    }

    public function logStartEnd(SubtaskEvent $event)
    {
        if (isset($event['status']) && $this->configModel->get('subtask_time_tracking') == 1) {
            $this->logger->debug('Subscriber executed: '.__METHOD__);
            $subtask = $this->subtaskModel->getById($event['id']);

            if (empty($subtask['user_id'])) {
                return false;
            }

            if ($subtask['status'] == SubtaskModel::STATUS_INPROGRESS) {
                return $this->subtaskTimeTrackingModel->logStartTime($subtask['id'], $subtask['user_id']);
            } else {
                return $this->subtaskTimeTrackingModel->logEndTime($subtask['id'], $subtask['user_id']);
            }
        }
    }
}
