<?php

require 'vendor/autoload.php';

// Automatically parse environment configuration (Heroku)
if (getenv('DATABASE_URL')) {

    $dbopts = parse_url(getenv('DATABASE_URL'));

    define('DB_DRIVER', $dbopts['scheme']);
    define('DB_USERNAME', $dbopts["user"]);
    define('DB_PASSWORD', $dbopts["pass"]);
    define('DB_HOSTNAME', $dbopts["host"]);
    define('DB_NAME', ltrim($dbopts["path"],'/'));
}

// Include custom config file
if (file_exists('config.php')) {
    require 'config.php';
}

require __DIR__.'/constants.php';

$container = new Pimple\Container;
$container->register(new ServiceProvider\LoggingProvider);
$container->register(new ServiceProvider\DatabaseProvider);
$container->register(new ServiceProvider\ClassProvider);
$container->register(new ServiceProvider\EventDispatcherProvider);
$container->register(new ServiceProvider\MailerProvider);
