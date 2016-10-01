<?php

namespace Kanboard\Subscriber;

use Kanboard\Event\TaskEvent;
use Kanboard\Model\TaskModel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TransitionSubscriber extends BaseSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            TaskModel::EVENT_MOVE_COLUMN => 'execute',
        );
    }

    public function execute(TaskEvent $event)
    {
        $this->logger->debug('Subscriber executed: '.__METHOD__);

        $user_id = $this->userSession->getId();

        if (! empty($user_id)) {
            $this->transitionModel->save($user_id, $event->getAll());
        }
    }
}
