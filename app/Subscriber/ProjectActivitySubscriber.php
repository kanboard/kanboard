<?php

namespace Kanboard\Subscriber;

use Kanboard\Event\GenericEvent;
use Kanboard\Model\Task;
use Kanboard\Model\Comment;
use Kanboard\Model\Subtask;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProjectActivitySubscriber extends \Kanboard\Core\Base implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            Task::EVENT_ASSIGNEE_CHANGE => array('execute', 0),
            Task::EVENT_UPDATE => array('execute', 0),
            Task::EVENT_CREATE => array('execute', 0),
            Task::EVENT_CLOSE => array('execute', 0),
            Task::EVENT_OPEN => array('execute', 0),
            Task::EVENT_MOVE_COLUMN => array('execute', 0),
            Task::EVENT_MOVE_POSITION => array('execute', 0),
            Task::EVENT_MOVE_SWIMLANE => array('execute', 0),
            Comment::EVENT_UPDATE => array('execute', 0),
            Comment::EVENT_CREATE => array('execute', 0),
            Subtask::EVENT_UPDATE => array('execute', 0),
            Subtask::EVENT_CREATE => array('execute', 0),
        );
    }

    public function execute(GenericEvent $event, $event_name)
    {
        // Executed only when someone is logged
        if ($this->userSession->isLogged() && isset($event['task_id'])) {
            $values = $this->getValues($event);

            $this->projectActivity->createEvent(
                $values['task']['project_id'],
                $values['task']['id'],
                $this->userSession->getId(),
                $event_name,
                $values
            );

            // Send notifications to third-party services
            foreach (array('slackWebhook', 'hipchatWebhook', 'jabber') as $model) {
                $this->$model->notify(
                    $values['task']['project_id'],
                    $values['task']['id'],
                    $event_name,
                    $values
                );
            }
        }
    }

    private function getValues(GenericEvent $event)
    {
        $values = array();
        $values['task'] = $this->taskFinder->getDetails($event['task_id']);
        $values['changes'] = isset($event['changes']) ? $event['changes'] : array();

        switch (get_class($event)) {
            case 'Event\SubtaskEvent':
                $values['subtask'] = $this->subtask->getById($event['id'], true);
                break;
            case 'Event\CommentEvent':
                $values['comment'] = $this->comment->getById($event['id']);
                break;
        }

        return $values;
    }
}
