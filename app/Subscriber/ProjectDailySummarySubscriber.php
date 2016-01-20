<?php

namespace Kanboard\Subscriber;

use Kanboard\Event\TaskEvent;
use Kanboard\Model\Task;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProjectDailySummarySubscriber extends BaseSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            Task::EVENT_CREATE_UPDATE => 'execute',
            Task::EVENT_CLOSE => 'execute',
            Task::EVENT_OPEN => 'execute',
            Task::EVENT_MOVE_COLUMN => 'execute',
            Task::EVENT_MOVE_SWIMLANE => 'execute',
        );
    }

    public function execute(TaskEvent $event)
    {
        if (isset($event['project_id']) && !$this->isExecuted()) {
            $this->logger->debug('Subscriber executed: '.__METHOD__);
            $this->projectDailyColumnStats->updateTotals($event['project_id'], date('Y-m-d'));
            $this->projectDailyStats->updateTotals($event['project_id'], date('Y-m-d'));
        }
    }
}
