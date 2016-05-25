<?php

namespace Kanboard\Subscriber;

use Kanboard\Event\GenericEvent;
use Kanboard\Job\NotificationJob;
use Kanboard\Model\Task;
use Kanboard\Model\Comment;
use Kanboard\Model\Subtask;
use Kanboard\Model\TaskFile;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NotificationSubscriber extends BaseSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            Task::EVENT_USER_MENTION    => 'handleEvent',
            Task::EVENT_CREATE          => 'handleEvent',
            Task::EVENT_UPDATE          => 'handleEvent',
            Task::EVENT_CLOSE           => 'handleEvent',
            Task::EVENT_OPEN            => 'handleEvent',
            Task::EVENT_MOVE_COLUMN     => 'handleEvent',
            Task::EVENT_MOVE_POSITION   => 'handleEvent',
            Task::EVENT_MOVE_SWIMLANE   => 'handleEvent',
            Task::EVENT_ASSIGNEE_CHANGE => 'handleEvent',
            Subtask::EVENT_CREATE       => 'handleEvent',
            Subtask::EVENT_UPDATE       => 'handleEvent',
            Comment::EVENT_CREATE       => 'handleEvent',
            Comment::EVENT_UPDATE       => 'handleEvent',
            Comment::EVENT_USER_MENTION => 'handleEvent',
            TaskFile::EVENT_CREATE      => 'handleEvent',
        );
    }

    public function handleEvent(GenericEvent $event, $eventName)
    {
        if (!$this->isExecuted($eventName)) {
            $this->logger->debug('Subscriber executed: ' . __METHOD__);

            $this->queueManager->push(NotificationJob::getInstance($this->container)
                ->withParams($event, $eventName, get_class($event))
            );
        }
    }
}
