<?php

require __DIR__.'/Core/Loader.php';
require __DIR__.'/helpers.php';
require __DIR__.'/translator.php';

require __DIR__.'/../vendor/SimpleValidator/Validator.php';
require __DIR__.'/../vendor/SimpleValidator/Base.php';
require __DIR__.'/../vendor/SimpleValidator/Validators/Required.php';
require __DIR__.'/../vendor/SimpleValidator/Validators/Unique.php';
require __DIR__.'/../vendor/SimpleValidator/Validators/MaxLength.php';
require __DIR__.'/../vendor/SimpleValidator/Validators/MinLength.php';
require __DIR__.'/../vendor/SimpleValidator/Validators/Integer.php';
require __DIR__.'/../vendor/SimpleValidator/Validators/Equals.php';
require __DIR__.'/../vendor/SimpleValidator/Validators/AlphaNumeric.php';
require __DIR__.'/../vendor/SimpleValidator/Validators/GreaterThan.php';
require __DIR__.'/../vendor/SimpleValidator/Validators/Date.php';
require __DIR__.'/../vendor/SimpleValidator/Validators/Email.php';
require __DIR__.'/../vendor/SimpleValidator/Validators/Numeric.php';

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

// Period (in second) to consider a task was modified recently
defined('RECENT_TASK_PERIOD') or define('RECENT_TASK_PERIOD', 48*60*60);

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
defined('LDAP_SSL_VERIFY') or define('LDAP_SSL_VERIFY', true);
defined('LDAP_USERNAME') or define('LDAP_USERNAME', null);
defined('LDAP_PASSWORD') or define('LDAP_PASSWORD', null);
defined('LDAP_ACCOUNT_BASE') or define('LDAP_ACCOUNT_BASE', '');
defined('LDAP_USER_PATTERN') or define('LDAP_USER_PATTERN', '');
defined('LDAP_ACCOUNT_FULLNAME') or define('LDAP_ACCOUNT_FULLNAME', 'displayname');
defined('LDAP_ACCOUNT_EMAIL') or define('LDAP_ACCOUNT_EMAIL', 'mail');

// Google authentication
defined('GOOGLE_AUTH') or define('GOOGLE_AUTH', false);
defined('GOOGLE_CLIENT_ID') or define('GOOGLE_CLIENT_ID', '');
defined('GOOGLE_CLIENT_SECRET') or define('GOOGLE_CLIENT_SECRET', '');

// GitHub authentication
defined('GITHUB_AUTH') or define('GITHUB_AUTH', false);
defined('GITHUB_CLIENT_ID') or define('GITHUB_CLIENT_ID', '');
defined('GITHUB_CLIENT_SECRET') or define('GITHUB_CLIENT_SECRET', '');

// Proxy authentication
defined('REVERSE_PROXY_AUTH') or define('REVERSE_PROXY_AUTH', false);
defined('REVERSE_PROXY_USER_HEADER') or define('REVERSE_PROXY_USER_HEADER', 'REMOTE_USER');
defined('REVERSE_PROXY_DEFAULT_ADMIN') or define('REVERSE_PROXY_DEFAULT_ADMIN', '');

// Mail configuration
defined('MAIL_FROM') or define('MAIL_FROM', 'notifications@kanboard.net');
defined('MAIL_TRANSPORT') or define('MAIL_TRANSPORT', 'mail');
defined('MAIL_SMTP_HOSTNAME') or define('MAIL_SMTP_HOSTNAME', '');
defined('MAIL_SMTP_PORT') or define('MAIL_SMTP_PORT', 25);
defined('MAIL_SMTP_USERNAME') or define('MAIL_SMTP_USERNAME', '');
defined('MAIL_SMTP_PASSWORD') or define('MAIL_SMTP_PASSWORD', '');
defined('MAIL_SMTP_ENCRYPTION') or define('MAIL_SMTP_ENCRYPTION', null);
defined('MAIL_SENDMAIL_COMMAND') or define('MAIL_SENDMAIL_COMMAND', '/usr/sbin/sendmail -bs');

$loader = new Loader;
$loader->execute();

$registry = new Registry;

$registry->db = function() use ($registry) {
    require __DIR__.'/../vendor/PicoDb/Database.php';

    switch (DB_DRIVER) {
        case 'sqlite':
            require __DIR__.'/Schema/Sqlite.php';

            $params = array(
                'driver' => 'sqlite',
                'filename' => DB_FILENAME
            );

            break;

        case 'mysql':
            require __DIR__.'/Schema/Mysql.php';

            $params = array(
                'driver'   => 'mysql',
                'hostname' => DB_HOSTNAME,
                'username' => DB_USERNAME,
                'password' => DB_PASSWORD,
                'database' => DB_NAME,
                'charset'  => 'utf8',
            );

            break;

        case 'postgres':
            require __DIR__.'/Schema/Postgres.php';

            $params = array(
                'driver'   => 'postgres',
                'hostname' => DB_HOSTNAME,
                'username' => DB_USERNAME,
                'password' => DB_PASSWORD,
                'database' => DB_NAME,
            );

            break;

        default:
            die('Database driver not supported');
    }

    $db = new \PicoDb\Database($params);

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

$registry->mailer = function() use ($registry) {

    require_once 'vendor/swiftmailer/swift_required.php';

    switch (MAIL_TRANSPORT) {
        case 'smtp':
            $transport = Swift_SmtpTransport::newInstance(MAIL_SMTP_HOSTNAME, MAIL_SMTP_PORT);
            $transport->setUsername(MAIL_SMTP_USERNAME);
            $transport->setPassword(MAIL_SMTP_PASSWORD);
            $transport->setEncryption(MAIL_SMTP_ENCRYPTION);
            break;
        case 'sendmail':
            $transport = Swift_SendmailTransport::newInstance(MAIL_SENDMAIL_COMMAND);
            break;
        default:
            $transport = Swift_MailTransport::newInstance();
    }

    return $transport;
};
