<?php

namespace ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogHandler;

class Logging implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $logger = new Logger('app');
        $logger->pushHandler(new StreamHandler(__DIR__.'/../../data/debug.log', Logger::DEBUG));
        $logger->pushHandler(new SyslogHandler('kanboard', LOG_USER, Logger::DEBUG));

        $container['logger'] = $logger;
    }
}
