<?php

namespace Kanboard\ServiceProvider;

use Kanboard\Job\CommentEventJob;
use Kanboard\Job\NotificationJob;
use Kanboard\Job\ProjectFileEventJob;
use Kanboard\Job\SubtaskEventJob;
use Kanboard\Job\TaskFileEventJob;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class JobProvider
 *
 * @package Kanboard\ServiceProvider
 * @author  Frederic Guillot
 */
class JobProvider implements ServiceProviderInterface
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
        $container['commentEventJob'] = $container->factory(function ($c) {
            return new CommentEventJob($c);
        });

        $container['subtaskEventJob'] = $container->factory(function ($c) {
            return new SubtaskEventJob($c);
        });

        $container['taskFileEventJob'] = $container->factory(function ($c) {
            return new TaskFileEventJob($c);
        });

        $container['projectFileEventJob'] = $container->factory(function ($c) {
            return new ProjectFileEventJob($c);
        });

        $container['notificationJob'] = $container->factory(function ($c) {
            return new NotificationJob($c);
        });

        return $container;
    }
}
