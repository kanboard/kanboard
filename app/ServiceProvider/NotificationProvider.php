<?php

namespace Kanboard\ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Kanboard\Model\UserNotificationType;
use Kanboard\Model\ProjectNotificationType;
use Kanboard\Notification\Mail as MailNotification;
use Kanboard\Notification\Web as WebNotification;

/**
 * Notification Provider
 *
 * @package serviceProvider
 * @author  Frederic Guillot
 */
class NotificationProvider implements ServiceProviderInterface
{
    /**
     * Register providers
     *
     * @access public
     * @param  \Pimple\Container $container
     * @return \Pimple\Container
     */
    public function register(Container $container)
    {
        $container['userNotificationType'] = function ($container) {
            $type = new UserNotificationType($container);
            $type->setType(MailNotification::TYPE, t('Email'), '\Kanboard\Notification\Mail');
            $type->setType(WebNotification::TYPE, t('Web'), '\Kanboard\Notification\Web');
            return $type;
        };

        $container['projectNotificationType'] = function ($container) {
            $type = new ProjectNotificationType($container);
            $type->setType('webhook', 'Webhook', '\Kanboard\Notification\Webhook', true);
            $type->setType('activity_stream', 'ActivityStream', '\Kanboard\Notification\ActivityStream', true);
            return $type;
        };

        return $container;
    }
}
