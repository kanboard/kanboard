<?php

namespace Kanboard\ServiceProvider;

use Kanboard\Core\Cache\FileCache;
use Kanboard\Core\Cache\MemoryCache;
use Kanboard\Decorator\ColumnMoveRestrictionCacheDecorator;
use Kanboard\Decorator\ColumnRestrictionCacheDecorator;
use Kanboard\Decorator\MetadataCacheDecorator;
use Kanboard\Decorator\ProjectRoleRestrictionCacheDecorator;
use Kanboard\Decorator\UserCacheDecorator;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Cache Provider
 *
 * @package Kanboard\ServiceProvider
 * @author  Frederic Guillot
 */
class CacheProvider implements ServiceProviderInterface
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
        $container['memoryCache'] = function() {
            return new MemoryCache();
        };

        if (CACHE_DRIVER === 'file') {
            $container['cacheDriver'] = function() {
                return new FileCache();
            };
        } else {
            $container['cacheDriver'] = $container['memoryCache'];
        }

        $container['userCacheDecorator'] = function($c) {
            return new UserCacheDecorator(
                $c['memoryCache'],
                $c['userModel']
            );
        };

        $container['userMetadataCacheDecorator'] = function($c) {
            return new MetadataCacheDecorator(
                $c['cacheDriver'],
                $c['userMetadataModel'],
                'user.metadata.',
                $c['userSession']->getId()
            );
        };

        $container['columnMoveRestrictionCacheDecorator'] = function($c) {
            return new ColumnMoveRestrictionCacheDecorator(
                $c['memoryCache'],
                $c['columnMoveRestrictionModel']
            );
        };

        $container['columnRestrictionCacheDecorator'] = function($c) {
            return new ColumnRestrictionCacheDecorator(
                $c['memoryCache'],
                $c['columnRestrictionModel']
            );
        };

        $container['projectRoleRestrictionCacheDecorator'] = function($c) {
            return new ProjectRoleRestrictionCacheDecorator(
                $c['memoryCache'],
                $c['projectRoleRestrictionModel']
            );
        };

        return $container;
    }
}
