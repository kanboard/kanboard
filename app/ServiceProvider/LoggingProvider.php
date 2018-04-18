<?php

namespace Kanboard\ServiceProvider;

use Psr\Log\LogLevel;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Kanboard\Core\Log\Logger;
use Kanboard\Core\Log\Stderr;
use Kanboard\Core\Log\Stdout;
use Kanboard\Core\Log\Syslog;
use Kanboard\Core\Log\File;
use Kanboard\Core\Log\System;

/**
 * Class LoggingProvider
 *
 * @package Kanboard\ServiceProvider
 * @author  Frederic Guillot
 */
class LoggingProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $logger = new Logger();
        $driver = null;

        switch (LOG_DRIVER) {
            case 'syslog':
                $driver = new Syslog('kanboard');
                break;
            case 'stdout':
                $driver = new Stdout();
                break;
            case 'stderr':
                $driver = new Stderr();
                break;
            case 'file':
                $driver = new File(LOG_FILE);
                break;
            case 'system':
                $driver = new System();
                break;
        }

        if ($driver !== null) {
            if (! DEBUG) {
                $driver->setLevel(LogLevel::INFO);
            }

            $logger->setLogger($driver);
        }

        $container['logger'] = $logger;
        return $container;
    }
}
