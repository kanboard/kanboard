<?php

namespace Kanboard\ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Kanboard\Core\Plugin\Loader;

/**
 * Plugin Provider
 *
 * @package Kanboard\ServiceProvider
 * @author  Frederic Guillot
 */
class PluginProvider implements ServiceProviderInterface
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
        $container['pluginLoader'] = new Loader($container);
        $container['pluginLoader']->scan();

        return $container;
    }
}
