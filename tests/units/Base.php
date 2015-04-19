<?php

require __DIR__.'/../../vendor/autoload.php';
require __DIR__.'/../../app/constants.php';

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcher;
use Symfony\Component\Stopwatch\Stopwatch;
use SimpleLogger\Logger;
use SimpleLogger\File;

date_default_timezone_set('UTC');

abstract class Base extends PHPUnit_Framework_TestCase
{
    protected $container;

    public function setUp()
    {
        if (DB_DRIVER === 'mysql') {
            $pdo = new PDO('mysql:host='.DB_HOSTNAME, DB_USERNAME, DB_PASSWORD);
            $pdo->exec('DROP DATABASE '.DB_NAME);
            $pdo->exec('CREATE DATABASE '.DB_NAME);
            $pdo = null;
        }
        else if (DB_DRIVER === 'postgres') {
            $pdo = new PDO('pgsql:host='.DB_HOSTNAME, DB_USERNAME, DB_PASSWORD);
            $pdo->exec('DROP DATABASE '.DB_NAME);
            $pdo->exec('CREATE DATABASE '.DB_NAME.' WITH OWNER '.DB_USERNAME);
            $pdo = null;
        }

        $this->container = new Pimple\Container;
        $this->container->register(new ServiceProvider\DatabaseProvider);
        $this->container->register(new ServiceProvider\ClassProvider);

        $this->container['dispatcher'] = new TraceableEventDispatcher(
            new EventDispatcher,
            new Stopwatch
        );

        $this->container['db']->log_queries = true;

        $this->container['logger'] = new Logger;
        $this->container['logger']->setLogger(new File('/dev/null'));
    }

    public function tearDown()
    {
        $this->container['db']->closeConnection();
    }
}
