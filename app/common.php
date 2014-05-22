<?php

require __DIR__.'/Core/Loader.php';
require __DIR__.'/helpers.php';
require __DIR__.'/translator.php';

use Core\Event;
use Core\Loader;
use Core\Registry;

// Include custom config file
if (file_exists('config.php')) {
    require 'config.php';
}

// Board refresh frequency in seconds for the public board view
defined('BOARD_PUBLIC_CHECK_INTERVAL') or define('BOARD_PUBLIC_CHECK_INTERVAL', 60);

// Board refresh frequency in seconds (the value 0 disable this feature)
defined('BOARD_CHECK_INTERVAL') or define('BOARD_CHECK_INTERVAL', 10);

// Custom session save path
defined('SESSION_SAVE_PATH') or define('SESSION_SAVE_PATH', '');

// Application version
defined('APP_VERSION') or define('APP_VERSION', 'master');

// Base directory
define('BASE_URL_DIRECTORY', dirname($_SERVER['PHP_SELF']));

// Database driver: sqlite or mysql
defined('DB_DRIVER') or define('DB_DRIVER', 'sqlite');

// Sqlite configuration
defined('DB_FILENAME') or define('DB_FILENAME', 'data/db.sqlite');

// Mysql configuration
defined('DB_USERNAME') or define('DB_USERNAME', 'root');
defined('DB_PASSWORD') or define('DB_PASSWORD', '');
defined('DB_HOSTNAME') or define('DB_HOSTNAME', 'localhost');
defined('DB_NAME') or define('DB_NAME', 'kanboard');

// LDAP configuration
defined('LDAP_AUTH') or define('LDAP_AUTH', false);
defined('LDAP_SERVER') or define('LDAP_SERVER', '');
defined('LDAP_PORT') or define('LDAP_PORT', 389);
defined('LDAP_USER_DN') or define('LDAP_USER_DN', '%s');

// Google authentication
defined('GOOGLE_AUTH') or define('GOOGLE_AUTH', false);
defined('GOOGLE_CLIENT_ID') or define('GOOGLE_CLIENT_ID', '');
defined('GOOGLE_CLIENT_SECRET') or define('GOOGLE_CLIENT_SECRET', '');

$loader = new Loader;
$loader->execute();

$registry = new Registry;

$registry->db = function() use ($registry) {
    require __DIR__.'/../vendor/PicoDb/Database.php';

    if (DB_DRIVER === 'sqlite') {

        require __DIR__.'/Schema/Sqlite.php';

        $db = new \PicoDb\Database(array(
            'driver' => 'sqlite',
            'filename' => DB_FILENAME
        ));
    }
    elseif (DB_DRIVER === 'mysql') {

        require __DIR__.'/Schema/Mysql.php';

        $db = new \PicoDb\Database(array(
            'driver'   => 'mysql',
            'hostname' => DB_HOSTNAME,
            'username' => DB_USERNAME,
            'password' => DB_PASSWORD,
            'database' => DB_NAME,
            'charset'  => 'utf8',
        ));
    }
    else {
        die('Database driver not supported');
    }

    if ($db->schema()->check(Schema\VERSION)) {
        return $db;
    }
    else {
        die('Unable to migrate database schema!');
    }
};

$registry->event = function() use ($registry) {
    return new Event;
};
