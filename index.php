<?php

require __DIR__.'/app/common.php';

if (! $container['router']->dispatch($_SERVER['REQUEST_URI'], isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '')) {
    die('Page not found!');
}
