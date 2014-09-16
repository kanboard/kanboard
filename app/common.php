<?php

// Common file between cli and web interface

require __DIR__.'/Core/Loader.php';
require __DIR__.'/helpers.php';
require __DIR__.'/functions.php';

use Core\Loader;
use Core\Registry;

// Include password_compat for PHP < 5.5
if (version_compare(PHP_VERSION, '5.5.0', '<')) {
    require __DIR__.'/../vendor/password.php';
}

// Include custom config file
if (file_exists('config.php')) {
    require 'config.php';
}

require __DIR__.'/constants.php';

$loader = new Loader;
$loader->setPath('app');
$loader->setPath('vendor');
$loader->execute();

$registry = new Registry;
$registry->db = setup_db();
$registry->event = setup_events();
$registry->mailer = function() { return setup_mailer(); };
