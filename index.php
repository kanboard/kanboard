<?php

require __DIR__.'/app/check_setup.php';
require __DIR__.'/app/common.php';

use Core\Router;

$router = new Router($registry);
$router->execute();
