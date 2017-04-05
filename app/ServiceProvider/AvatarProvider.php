<?php

namespace Kanboard\ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Kanboard\Core\User\Avatar\AvatarManager;
use Kanboard\User\Avatar\AvatarFileProvider;
use Kanboard\User\Avatar\LetterAvatarProvider;

/**
 * Avatar Provider
 *
 * @package Kanboard\ServiceProvider
 * @author  Frederic Guillot
 */
class AvatarProvider implements ServiceProviderInterface
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
        $container['avatarManager'] = new AvatarManager;
        $container['avatarManager']->register(new LetterAvatarProvider($container));
        $container['avatarManager']->register(new AvatarFileProvider($container));
        return $container;
    }
}
