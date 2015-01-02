<?php

namespace ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use SimpleLogger\Logger;
use SimpleLogger\Syslog;
use SimpleLogger\File;

class LoggingProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $logger = new Logger;
        $logger->setLogger(new Syslog('kanboard'));

        if (DEBUG) {
            $logger->setLogger(new File(__DIR__.'/../../data/debug.log'));
        }

        $container['logger'] = $logger;
    }
}
