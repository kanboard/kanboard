<?php

namespace Subscriber;

use Event\GenericEvent;
use Model\Task;
use Model\Comment;
use Model\SubTask;
use Model\File;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NotificationSubscriber extends Base implements EventSubscriberInterface
{
    private $templates = array(
        Task::EVENT_CREATE => 'task_creation',
        Task::EVENT_UPDATE => 'task_update',
        Task::EVENT_CLOSE => 'task_close',
        Task::EVENT_OPEN => 'task_open',
        Task::EVENT_MOVE_COLUMN => 'task_move_column',
        Task::EVENT_MOVE_POSITION => 'task_move_position',
        Task::EVENT_ASSIGNEE_CHANGE => 'task_assignee_change',
        SubTask::EVENT_CREATE => 'subtask_creation',
        SubTask::EVENT_UPDATE => 'subtask_update',
        Comment::EVENT_CREATE => 'comment_creation',
        Comment::EVENT_UPDATE => 'comment_update',
        File::EVENT_CREATE => 'file_creation',
    );

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
            SubTask::EVENT_CREATE => array('execute', 0),
            SubTask::EVENT_UPDATE => array('execute', 0),
            Comment::EVENT_CREATE => array('execute', 0),
            Comment::EVENT_UPDATE => array('execute', 0),
            File::EVENT_CREATE => array('execute', 0),
        );
    }

    public function execute(GenericEvent $event, $event_name)
    {
        $values = $this->getTemplateData($event);
        $users = $this->notification->getUsersList($values['task']['project_id']);

        if ($users) {
            $this->notification->sendEmails($this->templates[$event_name], $users, $values);
        }
    }

    public function getTemplateData(GenericEvent $event)
    {
        $values = array();

        switch (get_class($event)) {
            case 'Event\TaskEvent':
                $values['task'] = $this->taskFinder->getDetails($event['task_id']);
                break;
            case 'Event\SubtaskEvent':
                $values['subtask'] = $this->subTask->getById($event['id'], true);
                $values['task'] = $this->taskFinder->getDetails($event['task_id']);
                break;
            case 'Event\FileEvent':
                $values['file'] = $event->getAll();
                $values['task'] = $this->taskFinder->getDetails($event['task_id']);
                break;
            case 'Event\CommentEvent':
                $values['comment'] = $this->comment->getById($event['id']);
                $values['task'] = $this->taskFinder->getDetails($values['comment']['task_id']);
                break;
        }

        return $values;
    }
}
