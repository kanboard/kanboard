<?php

namespace Kanboard\ServiceProvider;

use Kanboard\Subscriber\LdapUserPhotoSubscriber;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Kanboard\Subscriber\AuthSubscriber;
use Kanboard\Subscriber\BootstrapSubscriber;
use Kanboard\Subscriber\NotificationSubscriber;
use Kanboard\Subscriber\ProjectDailySummarySubscriber;
use Kanboard\Subscriber\ProjectModificationDateSubscriber;
use Kanboard\Subscriber\TransitionSubscriber;
use Kanboard\Subscriber\RecurringTaskSubscriber;

/**
 * Class EventDispatcherProvider
 *
 * @package Kanboard\ServiceProvider
 * @author  Frederic Guillot
 */
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
        $container['dispatcher']->addSubscriber(new TransitionSubscriber($container));
        $container['dispatcher']->addSubscriber(new RecurringTaskSubscriber($container));

        if (LDAP_AUTH && LDAP_USER_ATTRIBUTE_PHOTO !== '') {
            $container['dispatcher']->addSubscriber(new LdapUserPhotoSubscriber($container));
        }

        return $container;
    }
}
