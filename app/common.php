<?php

require 'vendor/autoload.php';

// Include custom config file
if (file_exists('config.php')) {
    require 'config.php';
}

require __DIR__.'/constants.php';

$container = new Pimple\Container;
$container->register(new ServiceProvider\Logging);
$container->register(new ServiceProvider\Database);
$container->register(new ServiceProvider\Event);
$container->register(new ServiceProvider\Mailer);
