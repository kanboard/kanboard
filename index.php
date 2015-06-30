<?php

require __DIR__.'/app/common.php';

use Core\Router;

$router = new Router($container);
$router->execute();
