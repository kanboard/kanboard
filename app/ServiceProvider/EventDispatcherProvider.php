<?php

namespace ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Subscriber\AuthSubscriber;
use Subscriber\BootstrapSubscriber;
use Subscriber\NotificationSubscriber;
use Subscriber\ProjectActivitySubscriber;
use Subscriber\ProjectDailySummarySubscriber;
use Subscriber\ProjectModificationDateSubscriber;
use Subscriber\WebhookSubscriber;
use Subscriber\SubtaskTimesheetSubscriber;
use Subscriber\TaskMovedDateSubscriber;
use Subscriber\TransitionSubscriber;
use Subscriber\RecurringTaskSubscriber;

class EventDispatcherProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['dispatcher'] = new EventDispatcher;
        $container['dispatcher']->addSubscriber(new BootstrapSubscriber($container));
        $container['dispatcher']->addSubscriber(new AuthSubscriber($container));
        $container['dispatcher']->addSubscriber(new ProjectActivitySubscriber($container));
        $container['dispatcher']->addSubscriber(new ProjectDailySummarySubscriber($container));
        $container['dispatcher']->addSubscriber(new ProjectModificationDateSubscriber($container));
        $container['dispatcher']->addSubscriber(new WebhookSubscriber($container));
        $container['dispatcher']->addSubscriber(new NotificationSubscriber($container));
        $container['dispatcher']->addSubscriber(new SubtaskTimesheetSubscriber($container));
        $container['dispatcher']->addSubscriber(new TaskMovedDateSubscriber($container));
        $container['dispatcher']->addSubscriber(new TransitionSubscriber($container));
        $container['dispatcher']->addSubscriber(new RecurringTaskSubscriber($container));

        // Automatic actions
        $container['action']->attachEvents();
    }
}
