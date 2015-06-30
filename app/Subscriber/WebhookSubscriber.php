<?php

namespace Subscriber;

use Event\CommentEvent;
use Event\GenericEvent;
use Event\TaskEvent;
use Model\Comment;
use Model\Task;
use Model\File;
use Model\Subtask;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class WebhookSubscriber extends \Core\Base implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            Task::EVENT_CREATE => array('execute', 0),
            Task::EVENT_UPDATE => array('execute', 0),
            Task::EVENT_CLOSE => array('execute', 0),
            Task::EVENT_OPEN => array('execute', 0),
            Task::EVENT_MOVE_COLUMN => array('execute', 0),
            Task::EVENT_MOVE_POSITION => array('execute', 0),
            Task::EVENT_ASSIGNEE_CHANGE => array('execute', 0),
            Task::EVENT_MOVE_PROJECT => array('execute', 0),
            Task::EVENT_MOVE_SWIMLANE => array('execute', 0),
            Comment::EVENT_CREATE => array('execute', 0),
            Comment::EVENT_UPDATE => array('execute', 0),
            File::EVENT_CREATE => array('execute', 0),
            Subtask::EVENT_CREATE => array('execute', 0),
            Subtask::EVENT_UPDATE => array('execute', 0),
        );
    }

    public function execute(GenericEvent $event, $event_name)
    {
        $payload = array(
            'event_name' => $event_name,
            'event_data' => $event->getAll(),
        );

        $this->webhook->notify($payload);
    }
}
