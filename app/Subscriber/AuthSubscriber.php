<?php

namespace Subscriber;

use Core\Request;
use Event\AuthEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AuthSubscriber extends Base implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            'auth.success' => array('onSuccess', 0),
        );
    }

    public function onSuccess(AuthEvent $event)
    {
        $this->lastLogin->create(
            $event->getAuthType(),
            $event->getUserId(),
            Request::getIpAddress(),
            Request::getUserAgent()
        );
    }
}
