<?php

namespace Kanboard\ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Kanboard\Model\UserNotificationTypeModel;
use Kanboard\Model\ProjectNotificationTypeModel;
use Kanboard\Notification\MailNotification as MailNotification;
use Kanboard\Notification\WebNotification as WebNotification;

/**
 * Notification Provider
 *
 * @package Kanboard\ServiceProvider
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
        $container['userNotificationTypeModel'] = function ($container) {
            $type = new UserNotificationTypeModel($container);
            $type->setType(MailNotification::TYPE, t('Email'), '\Kanboard\Notification\MailNotification');
            $type->setType(WebNotification::TYPE, t('Web'), '\Kanboard\Notification\WebNotification');
            return $type;
        };

        $container['projectNotificationTypeModel'] = function ($container) {
            $type = new ProjectNotificationTypeModel($container);
            $type->setType('webhook', 'Webhook', '\Kanboard\Notification\WebhookNotification', true);
            $type->setType('activity_stream', 'ActivityStream', '\Kanboard\Notification\ActivityStreamNotification', true);
            return $type;
        };

        return $container;
    }
}
