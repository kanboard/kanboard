<?php

require __DIR__.'/../../vendor/autoload.php';
require __DIR__.'/../../app/constants.php';

date_default_timezone_set('UTC');

abstract class Base extends PHPUnit_Framework_TestCase
{
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
        $this->container->register(new ServiceProvider\Database);
        $this->container->register(new ServiceProvider\Event);
    }

    public function tearDown()
    {
        $this->container['db']->closeConnection();
    }
}
