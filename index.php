<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
require __DIR__.'/app/check_setup.php';
require __DIR__.'/app/common.php';

use Core\Router;

$router = new Router($container);
$router->execute();
