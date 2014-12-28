<?php

namespace Subscriber;

use Event\TaskEvent;
use Model\Task;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProjectDailySummarySubscriber extends Base implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            Task::EVENT_CREATE => array('execute', 0),
            Task::EVENT_CLOSE => array('execute', 0),
            Task::EVENT_OPEN => array('execute', 0),
            Task::EVENT_MOVE_COLUMN => array('execute', 0),
        );
    }

    public function execute(TaskEvent $event)
    {
        if (isset($event['project_id'])) {
            $this->projectDailySummary->updateTotals($event['project_id'], date('Y-m-d'));
        }
    }
}
