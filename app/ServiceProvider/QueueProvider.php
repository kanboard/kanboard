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
     * Registers services on the given container.
     *
     * @param Container $container
     */
    public function register(Container $container)
    {
        $container['queueManager'] = new QueueManager($container);
        return $container;
    }
}
