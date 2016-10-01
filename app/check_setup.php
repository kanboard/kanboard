<?php

// PHP 5.3.3 minimum
if (version_compare(PHP_VERSION, '5.3.3', '<')) {
    throw new Exception('This software require PHP 5.3.3 minimum');
}

// Checks for PHP < 5.4
if (version_compare(PHP_VERSION, '5.4.0', '<')) {

    // Short tags must be enabled for PHP < 5.4
    if (! ini_get('short_open_tag')) {
        throw new Exception('This software require to have short tags enabled if you have PHP < 5.4 ("short_open_tag = On")');
    }
}

// Check data folder if sqlite
if (DB_DRIVER === 'sqlite' && ! is_writable(dirname(DB_FILENAME))) {
    throw new Exception('The directory "'.dirname(DB_FILENAME).'" must be writeable by your web server user');
}

// Check PDO extensions
if (DB_DRIVER === 'sqlite' && ! extension_loaded('pdo_sqlite')) {
    throw new Exception('PHP extension required: "pdo_sqlite"');
}

if (DB_DRIVER === 'mysql' && ! extension_loaded('pdo_mysql')) {
    throw new Exception('PHP extension required: "pdo_mysql"');
}

if (DB_DRIVER === 'postgres' && ! extension_loaded('pdo_pgsql')) {
    throw new Exception('PHP extension required: "pdo_pgsql"');
}

// Check other extensions
foreach (array('gd', 'mbstring', 'hash', 'openssl', 'json', 'hash', 'ctype', 'filter', 'session') as $ext) {
    if (! extension_loaded($ext)) {
        throw new Exception('PHP extension required: "'.$ext.'"');
    }
}

// Fix wrong value for arg_separator.output, used by the function http_build_query()
if (ini_get('arg_separator.output') === '&amp;') {
    ini_set('arg_separator.output', '&');
}
