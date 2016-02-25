<?php

namespace Kanboard\Subscriber;

use Kanboard\Event\GenericEvent;
use Kanboard\Model\Task;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProjectModificationDateSubscriber extends BaseSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            Task::EVENT_CREATE_UPDATE => 'execute',
            Task::EVENT_CLOSE => 'execute',
            Task::EVENT_OPEN => 'execute',
            Task::EVENT_MOVE_SWIMLANE => 'execute',
            Task::EVENT_MOVE_COLUMN => 'execute',
            Task::EVENT_MOVE_POSITION => 'execute',
            Task::EVENT_MOVE_PROJECT => 'execute',
            Task::EVENT_ASSIGNEE_CHANGE => 'execute',
        );
    }

    public function execute(GenericEvent $event)
    {
        if (isset($event['project_id']) && !$this->isExecuted()) {
            $this->logger->debug('Subscriber executed: '.__METHOD__);
            $this->project->updateModificationDate($event['project_id']);
        }
    }
}
