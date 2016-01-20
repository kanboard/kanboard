<?php

namespace Kanboard\Subscriber;

use Kanboard\Event\GenericEvent;
use Kanboard\Model\Task;
use Kanboard\Model\Comment;
use Kanboard\Model\Subtask;
use Kanboard\Model\File;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NotificationSubscriber extends BaseSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            Task::EVENT_USER_MENTION => 'handleEvent',
            Task::EVENT_CREATE => 'handleEvent',
            Task::EVENT_UPDATE => 'handleEvent',
            Task::EVENT_CLOSE => 'handleEvent',
            Task::EVENT_OPEN => 'handleEvent',
            Task::EVENT_MOVE_COLUMN => 'handleEvent',
            Task::EVENT_MOVE_POSITION => 'handleEvent',
            Task::EVENT_MOVE_SWIMLANE => 'handleEvent',
            Task::EVENT_ASSIGNEE_CHANGE => 'handleEvent',
            Subtask::EVENT_CREATE => 'handleEvent',
            Subtask::EVENT_UPDATE => 'handleEvent',
            Comment::EVENT_CREATE => 'handleEvent',
            Comment::EVENT_UPDATE => 'handleEvent',
            Comment::EVENT_USER_MENTION => 'handleEvent',
            File::EVENT_CREATE => 'handleEvent',
        );
    }

    public function handleEvent(GenericEvent $event, $event_name)
    {
        if (! $this->isExecuted($event_name)) {
            $this->logger->debug('Subscriber executed: '.__METHOD__);
            $event_data = $this->getEventData($event);

            if (! empty($event_data)) {
                if (! empty($event['mention'])) {
                    $this->userNotification->sendUserNotification($event['mention'], $event_name, $event_data);
                } else {
                    $this->userNotification->sendNotifications($event_name, $event_data);
                    $this->projectNotification->sendNotifications($event_data['task']['project_id'], $event_name, $event_data);
                }
            }
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
