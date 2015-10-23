<?php

require __DIR__.'/../../vendor/autoload.php';
require __DIR__.'/../../app/constants.php';

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcher;
use Symfony\Component\Stopwatch\Stopwatch;
use SimpleLogger\Logger;
use SimpleLogger\File;

class FakeHttpClient
{
    private $url = '';
    private $data = array();
    private $headers = array();

    public function getUrl()
    {
        return $this->url;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function toPrettyJson()
    {
        return json_encode($this->data, JSON_PRETTY_PRINT);
    }

    public function postJson($url, array $data, array $headers = array())
    {
        $this->url = $url;
        $this->data = $data;
        $this->headers = $headers;
        return true;
    }

    public function postForm($url, array $data, array $headers = array())
    {
        $this->url = $url;
        $this->data = $data;
        $this->headers = $headers;
        return true;
    }
}

abstract class Base extends PHPUnit_Framework_TestCase
{
    protected $container;

    public function setUp()
    {
        date_default_timezone_set('UTC');

        if (DB_DRIVER === 'mysql') {
            $pdo = new PDO('mysql:host='.DB_HOSTNAME, DB_USERNAME, DB_PASSWORD);
            $pdo->exec('DROP DATABASE '.DB_NAME);
            $pdo->exec('CREATE DATABASE '.DB_NAME);
            $pdo = null;
        } elseif (DB_DRIVER === 'postgres') {
            $pdo = new PDO('pgsql:host='.DB_HOSTNAME, DB_USERNAME, DB_PASSWORD);
            $pdo->exec('DROP DATABASE '.DB_NAME);
            $pdo->exec('CREATE DATABASE '.DB_NAME.' WITH OWNER '.DB_USERNAME);
            $pdo = null;
        }

        $this->container = new Pimple\Container;
        $this->container->register(new Kanboard\ServiceProvider\DatabaseProvider);
        $this->container->register(new Kanboard\ServiceProvider\ClassProvider);

        $this->container['dispatcher'] = new TraceableEventDispatcher(
            new EventDispatcher,
            new Stopwatch
        );

        $this->container['db']->logQueries = true;

        $this->container['logger'] = new Logger;
        $this->container['logger']->setLogger(new File($this->isWindows() ? 'NUL' : '/dev/null'));
        $this->container['httpClient'] = new FakeHttpClient;
        $this->container['emailClient'] = $this->getMockBuilder('EmailClient')->setMethods(array('send'))->getMock();

        $this->container['userNotificationType'] = $this
            ->getMockBuilder('\Kanboard\Model\UserNotificationType')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array('getType', 'getSelectedTypes'))
            ->getMock();
    }

    public function tearDown()
    {
        $this->container['db']->closeConnection();
    }

    public function isWindows()
    {
        return substr(PHP_OS, 0, 3) === 'WIN';
    }
}
