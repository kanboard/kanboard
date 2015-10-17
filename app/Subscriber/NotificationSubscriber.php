<?php

namespace Kanboard\Subscriber;

use Kanboard\Event\GenericEvent;
use Kanboard\Model\Task;
use Kanboard\Model\Comment;
use Kanboard\Model\Subtask;
use Kanboard\Model\File;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NotificationSubscriber extends \Kanboard\Core\Base implements EventSubscriberInterface
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
        $event_data = $this->getEventData($event);

        if (! empty($event_data)) {
            $this->userNotification->sendNotifications($event_name, $event_data);
            $this->projectNotification->sendNotifications($event_data['task']['project_id'], $event_name, $event_data);
        }
    }

    public function getEventData(GenericEvent $event)
    {
        $values = array();

        if (! empty($event['changes'])) {
            $values['changes'] = $event['changes'];
        }

        switch (get_class($event)) {
            case 'Kanboard\Event\TaskEvent':
                $values['task'] = $this->taskFinder->getDetails($event['task_id']);
                break;
            case 'Kanboard\Event\SubtaskEvent':
                $values['subtask'] = $this->subtask->getById($event['id'], true);
                $values['task'] = $this->taskFinder->getDetails($values['subtask']['task_id']);
                break;
            case 'Kanboard\Event\FileEvent':
                $values['file'] = $event->getAll();
                $values['task'] = $this->taskFinder->getDetails($values['file']['task_id']);
                break;
            case 'Kanboard\Event\CommentEvent':
                $values['comment'] = $this->comment->getById($event['id']);
                $values['task'] = $this->taskFinder->getDetails($values['comment']['task_id']);
                break;
        }

        return $values;
    }
}
