<?php

require __DIR__.'/../vendor/autoload.php';

// Automatically parse environment configuration (Heroku)
if (getenv('DATABASE_URL')) {
    $dbopts = parse_url(getenv('DATABASE_URL'));

    define('DB_DRIVER', $dbopts['scheme']);
    define('DB_USERNAME', $dbopts["user"]);
    define('DB_PASSWORD', $dbopts["pass"]);
    define('DB_HOSTNAME', $dbopts["host"]);
    define('DB_PORT', isset($dbopts["port"]) ? $dbopts["port"] : null);
    define('DB_NAME', ltrim($dbopts["path"], '/'));
}

$config_file = implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'config.php'));

if (file_exists($config_file)) {
    require $config_file;
}

$config_file = implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'data', 'config.php'));

if (file_exists($config_file)) {
    require $config_file;
}

require __DIR__.'/constants.php';
require __DIR__.'/check_setup.php';

$container = new Pimple\Container;
$container->register(new Kanboard\ServiceProvider\HelperProvider);
$container->register(new Kanboard\ServiceProvider\SessionProvider);
$container->register(new Kanboard\ServiceProvider\LoggingProvider);
$container->register(new Kanboard\ServiceProvider\DatabaseProvider);
$container->register(new Kanboard\ServiceProvider\AuthenticationProvider);
$container->register(new Kanboard\ServiceProvider\NotificationProvider);
$container->register(new Kanboard\ServiceProvider\ClassProvider);
$container->register(new Kanboard\ServiceProvider\EventDispatcherProvider);
$container->register(new Kanboard\ServiceProvider\GroupProvider);
$container->register(new Kanboard\ServiceProvider\RouteProvider);
$container->register(new Kanboard\ServiceProvider\ActionProvider);
$container->register(new Kanboard\ServiceProvider\ExternalLinkProvider);
$container->register(new Kanboard\ServiceProvider\AvatarProvider);
$container->register(new Kanboard\ServiceProvider\FilterProvider);
$container->register(new Kanboard\ServiceProvider\PluginProvider);
