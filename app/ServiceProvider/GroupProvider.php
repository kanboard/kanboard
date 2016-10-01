<?php

namespace Kanboard\ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Kanboard\Core\Group\GroupManager;
use Kanboard\Group\DatabaseBackendGroupProvider;
use Kanboard\Group\LdapBackendGroupProvider;

/**
 * Group Provider
 *
 * @package Kanboard\ServiceProvider
 * @author  Frederic Guillot
 */
class GroupProvider implements ServiceProviderInterface
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
        $container['groupManager'] = new GroupManager;

        if (DB_GROUP_PROVIDER) {
            $container['groupManager']->register(new DatabaseBackendGroupProvider($container));
        }

        if (LDAP_AUTH && LDAP_GROUP_PROVIDER) {
            $container['groupManager']->register(new LdapBackendGroupProvider($container));
        }

        return $container;
    }
}
