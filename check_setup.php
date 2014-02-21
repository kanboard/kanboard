<?php

// PHP 5.3 minimum
if (version_compare(PHP_VERSION, '5.3.3', '<')) {
    die('This software require PHP 5.3.3 minimum');
}

// Short tags must be enabled for PHP < 5.4
if (version_compare(PHP_VERSION, '5.4.0', '<')) {

    if (! ini_get('short_open_tag')) {
        die('This software require to have short tags enabled, check your php.ini => "short_open_tag = On"');
    }
}

// Check extension: PDO Sqlite
if (! extension_loaded('pdo_sqlite')) {
    die('PHP extension required: pdo_sqlite');
}

// Check extension: mbstring
if (! extension_loaded('mbstring')) {
    die('PHP extension required: mbstring');
}

// Check if /data is writeable
if (! is_writable('data')) {
    die('The directory "data" must be writeable by your web server user');
}

// Include password_compat for PHP < 5.5
if (version_compare(PHP_VERSION, '5.5.0', '<')) {
    require __DIR__.'/vendor/password.php';
}
