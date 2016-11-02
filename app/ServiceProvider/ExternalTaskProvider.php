<?php

namespace Kanboard\ServiceProvider;

use Kanboard\Core\ExternalTask\ExternalTaskManager;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ExternalTaskProvider
 *
 * @package Kanboard\ServiceProvider
 * @author  Frederic Guillot
 */
class ExternalTaskProvider implements ServiceProviderInterface
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
        $container['externalTaskManager'] = new ExternalTaskManager();
        return $container;
    }
}
