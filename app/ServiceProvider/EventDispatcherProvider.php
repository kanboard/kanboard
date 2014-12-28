<?php

namespace ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Subscriber\NotificationSubscriber;
use Subscriber\ProjectActivitySubscriber;
use Subscriber\ProjectDailySummarySubscriber;
use Subscriber\ProjectModificationDateSubscriber;
use Subscriber\WebhookSubscriber;
use Symfony\Component\EventDispatcher\EventDispatcher;

class EventDispatcherProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['dispatcher'] = new EventDispatcher;
        $container['dispatcher']->addSubscriber(new ProjectActivitySubscriber($container));
        $container['dispatcher']->addSubscriber(new ProjectDailySummarySubscriber($container));
        $container['dispatcher']->addSubscriber(new ProjectModificationDateSubscriber($container));
        $container['dispatcher']->addSubscriber(new WebhookSubscriber($container));
        $container['dispatcher']->addSubscriber(new NotificationSubscriber($container));

        // Automatic actions
        $container['action']->attachEvents();
    }
}
