<?php

namespace Subscriber;

use Event\TaskEvent;
use Model\Task;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class WebhookSubscriber extends Base implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            Task::EVENT_CREATE => array('onTaskCreation', 0),
            Task::EVENT_UPDATE => array('onTaskModification', 0),
            Task::EVENT_CLOSE => array('onTaskModification', 0),
            Task::EVENT_OPEN => array('onTaskModification', 0),
            Task::EVENT_MOVE_COLUMN => array('onTaskModification', 0),
            Task::EVENT_MOVE_POSITION => array('onTaskModification', 0),
            Task::EVENT_ASSIGNEE_CHANGE => array('onTaskModification', 0),
        );
    }

    public function onTaskCreation(TaskEvent $event)
    {
        $this->executeRequest('webhook_url_task_creation', $event);
    }

    public function onTaskModification(TaskEvent $event)
    {
        $this->executeRequest('webhook_url_task_modification', $event);
    }

    public function executeRequest($parameter, TaskEvent $event)
    {
        $url = $this->config->get($parameter);

        if (! empty($url)) {
            $this->webhook->notify($url, $event->getAll());
        }
    }
}
