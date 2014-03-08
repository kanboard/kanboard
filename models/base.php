<?php

namespace Model;

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
require __DIR__.'/../vendor/PicoDb/Database.php';
require __DIR__.'/schema.php';

abstract class Base
{
    const APP_VERSION = 'master';
    const DB_VERSION  = 9;

    private static $dbInstance = null;
    protected $db;

    public function __construct()
    {
        if (self::$dbInstance === null) {
            self::$dbInstance = $this->getDatabaseInstance();
        }

        $this->db = self::$dbInstance;
    }

    public function getDatabaseInstance()
    {
        $db = new \PicoDb\Database(array(
            'driver' => 'sqlite',
            'filename' => DB_FILENAME
        ));

        if ($db->schema()->check(self::DB_VERSION)) {
            return $db;
        }
        else {
            die('Unable to migrate database schema!');
        }
    }

    // Generate a random token from /dev/urandom or with uniqid()
    public static function generateToken()
    {
        if (ini_get('open_basedir') === '' and strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
            $token = file_get_contents('/dev/urandom', false, null, 0, 30);
        }
        else {
            $token = uniqid(mt_rand(), true);
        }

        return hash('crc32b', $token);
    }

    public function getTimestampFromDate($value, $format)
    {
        $date = \DateTime::createFromFormat($format, $value);

        if ($date !== false) {
            $errors = \DateTime::getLastErrors();
            if ($errors['error_count'] === 0 && $errors['warning_count'] === 0) {
                $timestamp = $date->getTimestamp();
                return $timestamp > 0 ? $timestamp : 0;
            }
        }

        return 0;
    }
}
