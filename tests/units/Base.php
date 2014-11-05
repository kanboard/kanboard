<?php

require __DIR__.'/../../vendor/autoload.php';
require __DIR__.'/../../app/constants.php';

use Core\Loader;
use Core\Registry;

date_default_timezone_set('UTC');

abstract class Base extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->registry = new Registry;
        $this->registry->db = function() { return setup_db(); };
        $this->registry->event = function() { return setup_events(); };

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
    }

    public function tearDown()
    {
        $this->registry->shared('db')->closeConnection();
    }
}
