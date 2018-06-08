<?php

require __DIR__.'/../vendor/autoload.php';

$dbUrlParser = new PicoDb\UrlParser();

if ($dbUrlParser->isEnvironmentVariableDefined()) {
    $dbSettings = $dbUrlParser->getSettings();

    define('DB_DRIVER', $dbSettings['driver']);
    define('DB_USERNAME', $dbSettings['username']);
    define('DB_PASSWORD', $dbSettings['password']);
    define('DB_HOSTNAME', $dbSettings['hostname']);
    define('DB_PORT', $dbSettings['port']);
    define('DB_NAME', $dbSettings['database']);
}

$config_file = implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'data', 'config.php'));

if (file_exists($config_file)) {
    require $config_file;
}

$config_file = implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'config.php'));

if (file_exists($config_file)) {
    require $config_file;
}

require __DIR__.'/constants.php';
require __DIR__.'/check_setup.php';

$container = new Pimple\Container;
$container->register(new Kanboard\ServiceProvider\MailProvider());
$container->register(new Kanboard\ServiceProvider\HelperProvider());
$container->register(new Kanboard\ServiceProvider\SessionProvider());
$container->register(new Kanboard\ServiceProvider\LoggingProvider());
$container->register(new Kanboard\ServiceProvider\CacheProvider());
$container->register(new Kanboard\ServiceProvider\DatabaseProvider());
$container->register(new Kanboard\ServiceProvider\AuthenticationProvider());
$container->register(new Kanboard\ServiceProvider\NotificationProvider());
$container->register(new Kanboard\ServiceProvider\ClassProvider());
$container->register(new Kanboard\ServiceProvider\EventDispatcherProvider());
$container->register(new Kanboard\ServiceProvider\GroupProvider());
$container->register(new Kanboard\ServiceProvider\UserProvider());
$container->register(new Kanboard\ServiceProvider\RouteProvider());
$container->register(new Kanboard\ServiceProvider\ActionProvider());
$container->register(new Kanboard\ServiceProvider\ExternalLinkProvider());
$container->register(new Kanboard\ServiceProvider\ExternalTaskProvider());
$container->register(new Kanboard\ServiceProvider\AvatarProvider());
$container->register(new Kanboard\ServiceProvider\FilterProvider());
$container->register(new Kanboard\ServiceProvider\FormatterProvider());
$container->register(new Kanboard\ServiceProvider\JobProvider());
$container->register(new Kanboard\ServiceProvider\QueueProvider());
$container->register(new Kanboard\ServiceProvider\ApiProvider());
$container->register(new Kanboard\ServiceProvider\CommandProvider());
$container->register(new Kanboard\ServiceProvider\ObjectStorageProvider());
$container->register(new Kanboard\ServiceProvider\PluginProvider());
