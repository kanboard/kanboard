<?php

namespace Kanboard\ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Kanboard\Core\Session\SessionManager;
use Kanboard\Core\Session\SessionStorage;
use Kanboard\Core\Session\FlashMessage;

class SessionProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['sessionStorage'] = function() {
            return new SessionStorage;
        };

        $container['sessionManager'] = function($c) {
            return new SessionManager($c);
        };

        $container['flash'] = function($c) {
            return new FlashMessage($c);
        };

        return $container;
    }
}
