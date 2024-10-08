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

    protected function setUp(): void
    {
        date_default_timezone_set('UTC');
        $_SESSION = array();
        $test = get_class($this)."::".$this->getName();

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
        } elseif (DB_DRIVER === 'dblib') {
            $dsn = 'dblib:host='.DB_HOSTNAME;
            if (! empty(DB_PORT) ) { $dsn .= ','.DB_PORT; }
            $dsn .= ";dbname=master;appname=Kanboard Unit Tests [$test]";
            $pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD);
            $pdo->exec('use master;');
            $pdo->exec('DROP DATABASE IF EXISTS ['.DB_NAME.'];');
            $pdo->exec('CREATE DATABASE ['.DB_NAME.'];');
            $pdo->exec('ALTER DATABASE ['.DB_NAME.'] SET ALLOW_SNAPSHOT_ISOLATION ON;');
            $pdo->exec('ALTER DATABASE ['.DB_NAME.'] SET READ_COMMITTED_SNAPSHOT ON;');
            $pdo->exec('ALTER DATABASE ['.DB_NAME.'] SET ANSI_NULL_DEFAULT ON;');
            $pdo->exec('USE ['.DB_NAME.'];');
            $pdo = null;
        } elseif (DB_DRIVER === 'odbc') {
            $pdo = new PDO('odbc:'.DB_ODBC_DSN, DB_USERNAME, DB_PASSWORD);
            $pdo->exec('use master;');
            $pdo->exec('DROP DATABASE IF EXISTS ['.DB_NAME.'];');
            $pdo->exec('CREATE DATABASE ['.DB_NAME.'];');
            $pdo->exec('ALTER DATABASE ['.DB_NAME.'] SET ALLOW_SNAPSHOT_ISOLATION ON;');
            $pdo->exec('ALTER DATABASE ['.DB_NAME.'] SET READ_COMMITTED_SNAPSHOT ON;');
            $pdo->exec('ALTER DATABASE ['.DB_NAME.'] SET ANSI_NULL_DEFAULT ON;');
            $pdo->exec('USE ['.DB_NAME.'];');
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
        $this->container->register(new Kanboard\ServiceProvider\LoggingProvider());
        $this->container->register(new Kanboard\ServiceProvider\ExternalTaskProvider());

        $this->container['dispatcher'] = new TraceableEventDispatcher(
            new EventDispatcher,
            new Stopwatch
        );

        $this->dispatcher = $this->container['dispatcher'];

        $this->container['db']->getStatementHandler()->withLogging();
        $this->container['cli'] = new \Symfony\Component\Console\Application('Kanboard', 'test');

        $this->container['externalLinkManager'] = $this
            ->getMockBuilder('Kanboard\Core\ExternalLink\ExternalLinkManager')
            ->setConstructorArgs(array($this->container))
            ->getMock();

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

        $this->container['logger']->debug("Finished setUp() for test $test");
    }

    protected function tearDown(): void
    {
        $test = get_class($this)."::".$this->getName();
        foreach ($this->container['db']->getLogMessages() as $msg) {
            $this->container['logger']->debug('SQL: '.$msg);
        }
        $this->container['db']->closeConnection();
        $this->container['logger']->debug("Finishing tearDown() for test $test");
        unset ($this->container);
    }
}
