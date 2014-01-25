<?php

require __DIR__.'/check_setup.php';
require __DIR__.'/controllers/base.php';
require __DIR__.'/lib/router.php';

$router = new Router;
$router->execute();
