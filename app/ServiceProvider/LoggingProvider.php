<?php

namespace ServiceProvider;

use Psr\Log\LogLevel;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use SimpleLogger\Logger;
use SimpleLogger\Syslog;
use SimpleLogger\File;

class LoggingProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $syslog = new Syslog('kanboard');
        $syslog->setLevel(LogLevel::ERROR);

        $logger = new Logger;
        $logger->setLogger($syslog);

        if (DEBUG) {
            $logger->setLogger(new File(DEBUG_FILE));
        }

        $container['logger'] = $logger;
    }
}
