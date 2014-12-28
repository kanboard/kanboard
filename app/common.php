<?php

require 'vendor/autoload.php';

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
