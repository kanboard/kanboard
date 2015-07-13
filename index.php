<?php

require __DIR__.'/app/common.php';

if (! $container['router']->dispatch($_SERVER['REQUEST_URI'], $_SERVER['QUERY_STRING'])) {
    die('Page not found!');
}
