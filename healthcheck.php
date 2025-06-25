<?php

function send_response($status, $message)
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode(['status' => $status, 'message' => $message]);
    exit;
}

set_error_handler(function ($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

set_exception_handler(function ($exception) {
    send_response(503, $exception->getMessage());
});

require __DIR__.'/app/common.php';

$container['db']->getConnection()->query('SELECT 1');
send_response(200, 'Database connection is OK');
