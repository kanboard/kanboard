<?php

namespace Model;

require 'vendor/SimpleValidator/Validator.php';
require 'vendor/SimpleValidator/Base.php';
require 'vendor/SimpleValidator/Validators/Required.php';
require 'vendor/SimpleValidator/Validators/Unique.php';
require 'vendor/SimpleValidator/Validators/MaxLength.php';
require 'vendor/SimpleValidator/Validators/MinLength.php';
require 'vendor/SimpleValidator/Validators/Integer.php';
require 'vendor/SimpleValidator/Validators/Equals.php';
require 'vendor/SimpleValidator/Validators/AlphaNumeric.php';
require 'vendor/PicoDb/Database.php';
require __DIR__.'/schema.php';

abstract class Base
{
    const APP_VERSION = 'master';
    const DB_VERSION  = 1;
    const DB_FILENAME = 'data/db.sqlite';

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
            'filename' => self::DB_FILENAME
        ));

        if ($db->schema()->check(self::DB_VERSION)) {
            return $db;
        }
        else {
            die('Unable to migrate database schema!');
        }
    }
}
