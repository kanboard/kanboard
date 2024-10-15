<?php

use Kanboard\Core\Controller\Runner;

try {
    require __DIR__.'/app/common.php';
    $container['router']->dispatch();
    $runner = new Runner($container);
    $runner->execute();
} catch (Exception $e) {
    echo htmlspecialchars('Internal Error: '.$e->getMessage(), ENT_QUOTES, 'UTF-8', false);
}
