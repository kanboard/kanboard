<?php

namespace Kanboard\ServiceProvider;

use Kanboard\Core\Queue\QueueManager;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class QueueProvider
 *
 * @package Kanboard\ServiceProvider
 * @author  Frederic Guillot
 */
class QueueProvider implements ServiceProviderInterface
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
        $container['queueManager'] = new QueueManager($container);
        return $container;
    }
}
