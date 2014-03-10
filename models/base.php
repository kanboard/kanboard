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

abstract class Base
{
    protected $db;
    protected $event;

    public function __construct(\PicoDb\Database $db, \Core\Event $event)
    {
        $this->db = $db;
        $this->event = $event;
    }

    // Generate a random token from /dev/urandom or with uniqid()
    public static function generateToken()
    {
        if (ini_get('open_basedir') === '' && strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
            $token = file_get_contents('/dev/urandom', false, null, 0, 30);
        }
        else {
            $token = uniqid(mt_rand(), true);
        }

        return hash('crc32b', $token);
    }
}
