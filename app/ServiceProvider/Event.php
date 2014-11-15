<?php

namespace ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Core\Event as EventDispatcher;

class Event implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['event'] = new EventDispatcher;
    }
}
