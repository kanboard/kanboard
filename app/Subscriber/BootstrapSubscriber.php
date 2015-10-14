<?php

namespace Kanboard\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BootstrapSubscriber extends \Kanboard\Core\Base implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            'session.bootstrap' => array('setup', 0),
            'api.bootstrap' => array('setup', 0),
            'console.bootstrap' => array('setup', 0),
        );
    }

    public function setup()
    {
        $this->config->setupTranslations();
        $this->config->setupTimezone();
    }
}
