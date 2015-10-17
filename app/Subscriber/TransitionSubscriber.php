<?php

namespace Kanboard\Subscriber;

use Kanboard\Event\TaskEvent;
use Kanboard\Model\Task;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TransitionSubscriber extends \Kanboard\Core\Base implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            Task::EVENT_MOVE_COLUMN => array('execute', 0),
        );
    }

    public function execute(TaskEvent $event)
    {
        $user_id = $this->userSession->getId();

        if (! empty($user_id)) {
            $this->transition->save($user_id, $event->getAll());
        }
    }
}
