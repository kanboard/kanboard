<?php

require __DIR__.'/check_setup.php';
require __DIR__.'/controllers/base.php';
require __DIR__.'/lib/router.php';

if (file_exists('config.php')) require 'config.php';

// Auto-refresh frequency in seconds for the public board view
defined('AUTO_REFRESH_DURATION') or define('AUTO_REFRESH_DURATION', 60);

$router = new Router;
$router->execute();
