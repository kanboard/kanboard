<?php

namespace Kanboard\Subscriber;

use Kanboard\Event\TaskEvent;
use Kanboard\Model\Task;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProjectDailySummarySubscriber extends \Kanboard\Core\Base implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            Task::EVENT_CREATE => array('execute', 0),
            Task::EVENT_UPDATE => array('execute', 0),
            Task::EVENT_CLOSE => array('execute', 0),
            Task::EVENT_OPEN => array('execute', 0),
            Task::EVENT_MOVE_COLUMN => array('execute', 0),
        );
    }

    public function execute(TaskEvent $event)
    {
        if (isset($event['project_id'])) {
            $this->projectDailyColumnStats->updateTotals($event['project_id'], date('Y-m-d'));
            $this->projectDailyStats->updateTotals($event['project_id'], date('Y-m-d'));
        }
    }
}
