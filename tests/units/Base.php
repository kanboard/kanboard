<?php

namespace KanboardTests\units;

require __DIR__.'/../../app/constants.php';

use Composer\Autoload\ClassLoader;
use Kanboard\ServiceProvider\AuthenticationProvider;
use Kanboard\ServiceProvider\AvatarProvider;
use Kanboard\ServiceProvider\CacheProvider;
use Kanboard\ServiceProvider\ClassProvider;
use Kanboard\ServiceProvider\DatabaseProvider;
use Kanboard\ServiceProvider\ExternalTaskProvider;
use Kanboard\ServiceProvider\FilterProvider;
use Kanboard\ServiceProvider\FormatterProvider;
use Kanboard\ServiceProvider\HelperProvider;
use Kanboard\ServiceProvider\JobProvider;
use Kanboard\ServiceProvider\LoggingProvider;
use Kanboard\ServiceProvider\NotificationProvider;
use Kanboard\ServiceProvider\QueueProvider;
use Kanboard\ServiceProvider\RouteProvider;
use PDO;
use PHPUnit\Framework\TestCase;
use Pimple\Container;
use Symfony\Component\Console\Application;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcher;
use Symfony\Component\Stopwatch\Stopwatch;
use Kanboard\Core\Session\FlashMessage;
use Kanboard\ServiceProvider\ActionProvider;

abstract class Base extends TestCase
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
            if (! empty(DB_PORT)) {
                $dsn .= ','.DB_PORT;
            }
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

        $this->container = new Container;
        $this->container->register(new CacheProvider());
        $this->container->register(new HelperProvider());
        $this->container->register(new AuthenticationProvider());
        $this->container->register(new DatabaseProvider());
        $this->container->register(new ClassProvider());
        $this->container->register(new NotificationProvider());
        $this->container->register(new RouteProvider());
        $this->container->register(new AvatarProvider());
        $this->container->register(new FilterProvider());
        $this->container->register(new FormatterProvider());
        $this->container->register(new JobProvider());
        $this->container->register(new QueueProvider());
        $this->container->register(new LoggingProvider());
        $this->container->register(new ExternalTaskProvider());

        $this->container['dispatcher'] = new TraceableEventDispatcher(
            new EventDispatcher,
            new Stopwatch
        );

        $this->dispatcher = $this->container['dispatcher'];

        $this->container['db']->getStatementHandler()->withLogging();
        $this->container['cli'] = new Application('Kanboard', 'test');

        $this->container['externalLinkManager'] = $this
            ->getMockBuilder('Kanboard\Core\ExternalLink\ExternalLinkManager')
            ->setConstructorArgs(array($this->container))
            ->getMock();

        $this->container['httpClient'] = $this
            ->getMockBuilder('\Kanboard\Core\Http\Client')
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('get', 'getJson', 'postJson', 'postJsonAsync', 'postForm', 'postFormAsync'))
            ->getMock();

        $this->container['emailClient'] = $this
            ->getMockBuilder('\Kanboard\Core\Mail\Client')
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('send'))
            ->getMock();

        $this->container['userNotificationTypeModel'] = $this
            ->getMockBuilder('\Kanboard\Model\UserNotificationTypeModel')
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('getType', 'getSelectedTypes'))
            ->getMock();

        $this->container['objectStorage'] = $this
            ->getMockBuilder('\Kanboard\Core\ObjectStorage\FileStorage')
            ->setConstructorArgs([sys_get_temp_dir()])
            ->onlyMethods(array('put', 'moveFile', 'remove', 'moveUploadedFile'))
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
        unset($this->container);
    }
}
