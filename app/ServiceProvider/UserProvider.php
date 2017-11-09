<?php

namespace Kanboard\ServiceProvider;

use Kanboard\Core\User\UserManager;
use Kanboard\User\DatabaseBackendUserProvider;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * User Provider
 *
 * @package Kanboard\ServiceProvider
 * @author  Frederic Guillot
 */
class UserProvider implements ServiceProviderInterface
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
        $container['userManager'] = new UserManager();

        if (DB_USER_PROVIDER) {
            $container['userManager']->register(new DatabaseBackendUserProvider($container));
        }

        return $container;
    }
}
