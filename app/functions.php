<?php

use Core\Event;
use Core\Translator;
use PicoDb\Database;

/**
 * Send a debug message to the log files
 *
 * @param mixed $message Variable or string
 */
function debug($message)
{
    if (! is_string($message)) {
        $message = var_export($message, true);
    }

    error_log($message.PHP_EOL, 3, 'data/debug.log');
}

/**
 * Setup events
 *
 * @return Core\Event
 */
function setup_events()
{
    return new Event;
}

/**
 * Setup the mailer according to the configuration
 *
 * @return Swift_SmtpTransport
 */
function setup_mailer()
{
    require_once __DIR__.'/../vendor/swiftmailer/swift_required.php';

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
}

/**
 * Setup the database driver and execute schema migration
 *
 * @return PicoDb\Database
 */
function setup_db()
{
    switch (DB_DRIVER) {
        case 'sqlite':
            $db = setup_sqlite();
            break;

        case 'mysql':
            $db = setup_mysql();
            break;

        case 'postgres':
            $db = setup_postgres();
            break;

        default:
            die('Database driver not supported');
    }

    if ($db->schema()->check(Schema\VERSION)) {
        return $db;
    }
    else {
        $errors = $db->getLogMessages();
        die('Unable to migrate database schema: <br/><br/><strong>'.(isset($errors[0]) ? $errors[0] : 'Unknown error').'</strong>');
    }
}

/**
 * Setup the Sqlite database driver
 *
 * @return PicoDb\Database
 */
function setup_sqlite()
{
    require_once __DIR__.'/Schema/Sqlite.php';

    return new Database(array(
        'driver' => 'sqlite',
        'filename' => DB_FILENAME
    ));
}

/**
 * Setup the Mysql database driver
 *
 * @return PicoDb\Database
 */
function setup_mysql()
{
    require_once __DIR__.'/Schema/Mysql.php';

    return new Database(array(
        'driver'   => 'mysql',
        'hostname' => DB_HOSTNAME,
        'username' => DB_USERNAME,
        'password' => DB_PASSWORD,
        'database' => DB_NAME,
        'charset'  => 'utf8',
    ));
}

/**
 * Setup the Postgres database driver
 *
 * @return PicoDb\Database
 */
function setup_postgres()
{
    require_once __DIR__.'/Schema/Postgres.php';

    return new Database(array(
        'driver'   => 'postgres',
        'hostname' => DB_HOSTNAME,
        'username' => DB_USERNAME,
        'password' => DB_PASSWORD,
        'database' => DB_NAME,
    ));
}

/**
 * Translate a string
 *
 * @return string
 */
function t()
{
    $t = new Translator;
    return call_user_func_array(array($t, 'translate'), func_get_args());
}

/**
 * Translate a string with no HTML escaping
 *
 * @return string
 */
function e()
{
    $t = new Translator;
    return call_user_func_array(array($t, 'translateNoEscaping'), func_get_args());
}

/**
 * Translate a currency
 *
 * @return string
 */
function c($value)
{
    $t = new Translator;
    return $t->currency($value);
}

/**
 * Translate a number
 *
 * @return string
 */
function n($value)
{
    $t = new Translator;
    return $t->number($value);
}

/**
 * Translate a date
 *
 * @return string
 */
function dt($format, $timestamp)
{
    $t = new Translator;
    return $t->datetime($format, $timestamp);
}

/**
 * Handle plurals, return $t2 if $value > 1
 *
 * @todo   Improve this function
 * @return mixed
 */
function p($value, $t1, $t2) {
    return $value > 1 ? $t2 : $t1;
}
