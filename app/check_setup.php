<?php

// PHP 7.2.0 minimum
if (version_compare(PHP_VERSION, '7.2.0', '<')) {
    throw new Exception('This software requires PHP 7.2.0 minimum');
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
foreach (array('gd', 'mbstring', 'hash', 'openssl', 'json', 'hash', 'ctype', 'filter', 'session', 'dom', 'filter', 'SimpleXML', 'xml') as $ext) {
    if (! extension_loaded($ext)) {
        throw new Exception('This PHP extension is required: "'.$ext.'"');
    }
}

// Fix wrong value for arg_separator.output, used by the function http_build_query()
if (ini_get('arg_separator.output') === '&amp;') {
    ini_set('arg_separator.output', '&');
}

// Make sure we can read files with "\r", "\r\n" and "\n"
if (ini_get('auto_detect_line_endings') != 1) {
    ini_set("auto_detect_line_endings", 1);
}
