<?php

namespace Kanboard\ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Kanboard\Subscriber\AuthSubscriber;
use Kanboard\Subscriber\BootstrapSubscriber;
use Kanboard\Subscriber\NotificationSubscriber;
use Kanboard\Subscriber\ProjectDailySummarySubscriber;
use Kanboard\Subscriber\ProjectModificationDateSubscriber;
use Kanboard\Subscriber\SubtaskTimeTrackingSubscriber;
use Kanboard\Subscriber\TaskMovedDateSubscriber;
use Kanboard\Subscriber\TransitionSubscriber;
use Kanboard\Subscriber\RecurringTaskSubscriber;

class EventDispatcherProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['dispatcher'] = new EventDispatcher;
        $container['dispatcher']->addSubscriber(new BootstrapSubscriber($container));
        $container['dispatcher']->addSubscriber(new AuthSubscriber($container));
        $container['dispatcher']->addSubscriber(new ProjectDailySummarySubscriber($container));
        $container['dispatcher']->addSubscriber(new ProjectModificationDateSubscriber($container));
        $container['dispatcher']->addSubscriber(new NotificationSubscriber($container));
        $container['dispatcher']->addSubscriber(new SubtaskTimeTrackingSubscriber($container));
        $container['dispatcher']->addSubscriber(new TaskMovedDateSubscriber($container));
        $container['dispatcher']->addSubscriber(new TransitionSubscriber($container));
        $container['dispatcher']->addSubscriber(new RecurringTaskSubscriber($container));

        // Automatic actions
        $container['action']->attachEvents();
    }
}
