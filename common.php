<?php

require __DIR__.'/core/registry.php';
require __DIR__.'/core/helper.php';
require __DIR__.'/core/translator.php';

$registry = new Core\Registry;

$registry->db_version = 11;

$registry->db = function() use ($registry) {
    require __DIR__.'/vendor/PicoDb/Database.php';

    if (DB_DRIVER === 'sqlite') {

        require __DIR__.'/schemas/sqlite.php';

        $db = new \PicoDb\Database(array(
            'driver' => 'sqlite',
            'filename' => DB_FILENAME
        ));
    }
    elseif (DB_DRIVER === 'mysql') {

        require __DIR__.'/schemas/mysql.php';

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

    if ($db->schema()->check($registry->db_version)) {
        return $db;
    }
    else {
        die('Unable to migrate database schema!');
    }
};

$registry->event = function() use ($registry) {
    require __DIR__.'/core/event.php';
    return new \Core\Event;
};

$registry->action = function() use ($registry) {
    require_once __DIR__.'/models/action.php';
    return new \Model\Action($registry->shared('db'), $registry->shared('event'));
};

$registry->config = function() use ($registry) {
    require_once __DIR__.'/models/config.php';
    return new \Model\Config($registry->shared('db'), $registry->shared('event'));
};

$registry->acl = function() use ($registry) {
    require_once __DIR__.'/models/acl.php';
    return new \Model\Acl($registry->shared('db'), $registry->shared('event'));
};

$registry->user = function() use ($registry) {
    require_once __DIR__.'/models/user.php';
    return new \Model\User($registry->shared('db'), $registry->shared('event'));
};

$registry->comment = function() use ($registry) {
    require_once __DIR__.'/models/comment.php';
    return new \Model\Comment($registry->shared('db'), $registry->shared('event'));
};

$registry->task = function() use ($registry) {
    require_once __DIR__.'/models/task.php';
    return new \Model\Task($registry->shared('db'), $registry->shared('event'));
};

$registry->board = function() use ($registry) {
    require_once __DIR__.'/models/board.php';
    return new \Model\Board($registry->shared('db'), $registry->shared('event'));
};

$registry->project = function() use ($registry) {
    require_once __DIR__.'/models/project.php';
    return new \Model\Project($registry->shared('db'), $registry->shared('event'));
};

$registry->action = function() use ($registry) {
    require_once __DIR__.'/models/action.php';
    return new \Model\Action($registry->shared('db'), $registry->shared('event'));
};

if (file_exists('config.php')) require 'config.php';

// Auto-refresh frequency in seconds for the public board view
defined('AUTO_REFRESH_DURATION') or define('AUTO_REFRESH_DURATION', 60);

// Custom session save path
defined('SESSION_SAVE_PATH') or define('SESSION_SAVE_PATH', '');

// Application version
defined('APP_VERSION') or define('APP_VERSION', 'master');

// Database driver: sqlite or mysql
defined('DB_DRIVER') or define('DB_DRIVER', 'sqlite');

// Sqlite configuration
defined('DB_FILENAME') or define('DB_FILENAME', 'data/db.sqlite');

// Mysql configuration
defined('DB_USERNAME') or define('DB_USERNAME', 'root');
defined('DB_PASSWORD') or define('DB_PASSWORD', '');
defined('DB_HOSTNAME') or define('DB_HOSTNAME', 'localhost');
defined('DB_NAME') or define('DB_NAME', 'kanboard');
