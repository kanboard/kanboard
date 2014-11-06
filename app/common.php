<?php

// Common file between cli and web interface

require 'vendor/autoload.php';

// Include custom config file
if (file_exists('config.php')) {
    require 'config.php';
}

require __DIR__.'/constants.php';

$registry = new Core\Registry;
$registry->db = setup_db();
$registry->event = setup_events();
$registry->mailer = function() { return setup_mailer(); };
