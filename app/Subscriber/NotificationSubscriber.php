<?php

namespace Subscriber;

use Event\GenericEvent;
use Model\Task;
use Model\Comment;
use Model\Subtask;
use Model\File;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NotificationSubscriber extends \Core\Base implements EventSubscriberInterface
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
            Task::EVENT_MOVE_SWIMLANE => array('execute', 0),
            Task::EVENT_ASSIGNEE_CHANGE => array('execute', 0),
            Subtask::EVENT_CREATE => array('execute', 0),
            Subtask::EVENT_UPDATE => array('execute', 0),
            Comment::EVENT_CREATE => array('execute', 0),
            Comment::EVENT_UPDATE => array('execute', 0),
            File::EVENT_CREATE => array('execute', 0),
        );
    }

    public function execute(GenericEvent $event, $event_name)
    {
        $this->notification->sendNotifications($event_name, $this->getEventData($event));
    }

    public function getEventData(GenericEvent $event)
    {
        $values = array();

        switch (get_class($event)) {
            case 'Event\TaskEvent':
                $values['task'] = $this->taskFinder->getDetails($event['task_id']);
                $values['changes'] = isset($event['changes']) ? $event['changes'] : array();
                break;
            case 'Event\SubtaskEvent':
                $values['subtask'] = $this->subtask->getById($event['id'], true);
                $values['task'] = $this->taskFinder->getDetails($values['subtask']['task_id']);
                break;
            case 'Event\FileEvent':
                $values['file'] = $event->getAll();
                $values['task'] = $this->taskFinder->getDetails($values['file']['task_id']);
                break;
            case 'Event\CommentEvent':
                $values['comment'] = $this->comment->getById($event['id']);
                $values['task'] = $this->taskFinder->getDetails($values['comment']['task_id']);
                break;
        }

        return $values;
    }
}
