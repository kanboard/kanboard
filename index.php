<?php

require __DIR__.'/check_setup.php';
require __DIR__.'/common.php';
require __DIR__.'/core/router.php';

$router = new Core\Router($registry);
$router->execute();
