<?php

require __DIR__.'/../../vendor/autoload.php';
require __DIR__.'/../../app/constants.php';

use Composer\Autoload\ClassLoader;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcher;
use Symfony\Component\Stopwatch\Stopwatch;
use Kanboard\Core\Log\Logger;
use Kanboard\Core\Session\FlashMessage;
use Kanboard\ServiceProvider\ActionProvider;

abstract class Base extends PHPUnit\Framework\TestCase
{
    protected $container;

    /**
     * @var EventDispatcher
     */
    protected $dispatcher;

    public function setUp()
    {
        date_default_timezone_set('UTC');
        $_SESSION = array();

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
        $this->container->register(new Kanboard\ServiceProvider\CacheProvider());
        $this->container->register(new Kanboard\ServiceProvider\HelperProvider());
        $this->container->register(new Kanboard\ServiceProvider\AuthenticationProvider());
        $this->container->register(new Kanboard\ServiceProvider\DatabaseProvider());
        $this->container->register(new Kanboard\ServiceProvider\ClassProvider());
        $this->container->register(new Kanboard\ServiceProvider\NotificationProvider());
        $this->container->register(new Kanboard\ServiceProvider\RouteProvider());
        $this->container->register(new Kanboard\ServiceProvider\AvatarProvider());
        $this->container->register(new Kanboard\ServiceProvider\FilterProvider());
        $this->container->register(new Kanboard\ServiceProvider\FormatterProvider());
        $this->container->register(new Kanboard\ServiceProvider\JobProvider());
        $this->container->register(new Kanboard\ServiceProvider\QueueProvider());
        $this->container->register(new Kanboard\ServiceProvider\ExternalTaskProvider());

        $this->container['dispatcher'] = new TraceableEventDispatcher(
            new EventDispatcher,
            new Stopwatch
        );

        $this->dispatcher = $this->container['dispatcher'];

        $this->container['db']->getStatementHandler()->withLogging();
        $this->container['logger'] = new Logger();
        $this->container['cli'] = new \Symfony\Component\Console\Application('Kanboard', 'test');

        $this->container['httpClient'] = $this
            ->getMockBuilder('\Kanboard\Core\Http\Client')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array('get', 'getJson', 'postJson', 'postJsonAsync', 'postForm', 'postFormAsync'))
            ->getMock();

        $this->container['emailClient'] = $this
            ->getMockBuilder('\Kanboard\Core\Mail\Client')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array('send'))
            ->getMock();

        $this->container['userNotificationTypeModel'] = $this
            ->getMockBuilder('\Kanboard\Model\UserNotificationTypeModel')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array('getType', 'getSelectedTypes'))
            ->getMock();

        $this->container['objectStorage'] = $this
            ->getMockBuilder('\Kanboard\Core\ObjectStorage\FileStorage')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array('put', 'moveFile', 'remove', 'moveUploadedFile'))
            ->getMock();

        $this->container->register(new ActionProvider);

        $this->container['flash'] = function ($c) {
            return new FlashMessage($c);
        };

        $loader = new ClassLoader();
        $loader->addPsr4('Kanboard\Plugin\\', PLUGINS_DIR);
        $loader->register();
    }

    public function tearDown()
    {
        $this->container['db']->closeConnection();
        unset ($this->container);
    }
}
