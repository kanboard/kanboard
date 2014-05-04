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
require __DIR__.'/../vendor/SimpleValidator/Validators/Email.php';

/**
 * Base model class
 *
 * @package  model
 * @author   Frederic Guillot
 */
abstract class Base
{
    /**
     * Database instance
     *
     * @access protected
     * @var PicoDb
     */
    protected $db;

    /**
     * Event dispatcher instance
     *
     * @access protected
     * @var Core\Event
     */
    protected $event;

    /**
     * Constructor
     *
     * @access public
     * @param  PicoDb\Database  $db     Database instance
     * @param  \Core\Event       $event  Event dispatcher instance
     */
    public function __construct(\PicoDb\Database $db, \Core\Event $event)
    {
        $this->db = $db;
        $this->event = $event;
    }

    /**
     * Generate a random token with different methods: openssl or /dev/urandom or fallback to uniqid()
     *
     * @static
     * @access public
     * @return string  Random token
     */
    public static function generateToken()
    {
        if (function_exists('openssl_random_pseudo_bytes')) {
            return bin2hex(\openssl_random_pseudo_bytes(16));
        }
        else if (ini_get('open_basedir') === '' && strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
            return hash('sha256', file_get_contents('/dev/urandom', false, null, 0, 30));
        }

        return hash('sha256', uniqid(mt_rand(), true));
    }
}
