<?php

use Core\Event;
use Core\Translator;
use PicoDb\Database;

function debug($message)
{
    error_log($message.PHP_EOL, 3, 'data/debug.log');
}

function setup_events()
{
    return new Event;
}

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

function setup_db()
{
    switch (DB_DRIVER) {
        case 'sqlite':
            require_once __DIR__.'/Schema/Sqlite.php';

            $params = array(
                'driver' => 'sqlite',
                'filename' => DB_FILENAME
            );

            break;

        case 'mysql':
            require_once __DIR__.'/Schema/Mysql.php';

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
            require_once __DIR__.'/Schema/Postgres.php';

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

    $db = new Database($params);

    if ($db->schema()->check(Schema\VERSION)) {
        return $db;
    }
    else {
        $errors = $db->getLogMessages();
        die('Unable to migrate database schema: <br/><br/><strong>'.(isset($errors[0]) ? $errors[0] : 'Unknown error').'</strong>');
    }
}

// Get a translation
function t()
{
    $t = new Translator;
    return call_user_func_array(array($t, 'translate'), func_get_args());
}

// translate with no html escaping
function e()
{
    $t = new Translator;
    return call_user_func_array(array($t, 'translateNoEscaping'), func_get_args());
}

// Get a locale currency
function c($value)
{
    $t = new Translator;
    return $t->currency($value);
}

// Get a formatted number
function n($value)
{
    $t = new Translator;
    return $t->number($value);
}

// Get a locale date
function dt($format, $timestamp)
{
    $t = new Translator;
    return $t->datetime($format, $timestamp);
}

// Plurals, return $t2 if $value > 1
function p($value, $t1, $t2) {
    return $value > 1 ? $t2 : $t1;
}
