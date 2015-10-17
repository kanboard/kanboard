<?php

require dirname(__DIR__) . '/vendor/autoload.php';

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

// Include custom config file
if (file_exists('config.php')) {
    require 'config.php';
}

require __DIR__.'/constants.php';
require __DIR__.'/check_setup.php';

$container = new Pimple\Container;
$container->register(new Kanboard\ServiceProvider\LoggingProvider);
$container->register(new Kanboard\ServiceProvider\DatabaseProvider);
$container->register(new Kanboard\ServiceProvider\ClassProvider);
$container->register(new Kanboard\ServiceProvider\EventDispatcherProvider);

if (ENABLE_URL_REWRITE) {
    require __DIR__.'/routes.php';
}

$container['pluginLoader']->scan();
