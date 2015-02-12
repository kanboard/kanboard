<?php

namespace Subscriber;

use Event\TaskEvent;
use Model\Task;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TaskMovedDateSubscriber extends Base implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            Task::EVENT_MOVE_COLUMN => array('execute', 0),
        );
    }

    public function execute(TaskEvent $event)
    {
        if (isset($event['task_id'])) {
            $this->container['db']->table(Task::TABLE)->eq('id', $event['task_id'])->update(array('date_moved' => time()));
        }
    }
}
