<?php

try {
    require __DIR__.'/app/common.php';
    $container['router']->dispatch($_SERVER['REQUEST_URI'], isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '');
} catch (Exception $e) {
    echo 'Internal Error: '.$e->getMessage();
}
