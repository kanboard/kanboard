<?php

namespace Subscriber;

use Event\GenericEvent;
use Model\Task;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProjectModificationDateSubscriber extends Base implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            Task::EVENT_CREATE_UPDATE => array('execute', 0),
            Task::EVENT_CLOSE => array('execute', 0),
            Task::EVENT_OPEN => array('execute', 0),
            Task::EVENT_MOVE_SWIMLANE => array('execute', 0),
            Task::EVENT_MOVE_COLUMN => array('execute', 0),
            Task::EVENT_MOVE_POSITION => array('execute', 0),
            Task::EVENT_MOVE_PROJECT => array('execute', 0),
            Task::EVENT_ASSIGNEE_CHANGE => array('execute', 0),
        );
    }

    public function execute(GenericEvent $event)
    {
        if (isset($event['project_id'])) {
            $this->project->updateModificationDate($event['project_id']);
        }
    }
}
