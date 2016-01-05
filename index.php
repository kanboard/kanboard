<?php

try {
    require __DIR__.'/app/common.php';
    $container['router']->dispatch();
} catch (Exception $e) {
    echo 'Internal Error: '.$e->getMessage();
}
