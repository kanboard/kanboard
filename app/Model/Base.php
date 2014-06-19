<?php

namespace Model;

require __DIR__.'/../../vendor/SimpleValidator/Validator.php';
require __DIR__.'/../../vendor/SimpleValidator/Base.php';
require __DIR__.'/../../vendor/SimpleValidator/Validators/Required.php';
require __DIR__.'/../../vendor/SimpleValidator/Validators/Unique.php';
require __DIR__.'/../../vendor/SimpleValidator/Validators/MaxLength.php';
require __DIR__.'/../../vendor/SimpleValidator/Validators/MinLength.php';
require __DIR__.'/../../vendor/SimpleValidator/Validators/Integer.php';
require __DIR__.'/../../vendor/SimpleValidator/Validators/Equals.php';
require __DIR__.'/../../vendor/SimpleValidator/Validators/AlphaNumeric.php';
require __DIR__.'/../../vendor/SimpleValidator/Validators/GreaterThan.php';
require __DIR__.'/../../vendor/SimpleValidator/Validators/Date.php';
require __DIR__.'/../../vendor/SimpleValidator/Validators/Email.php';
require __DIR__.'/../../vendor/SimpleValidator/Validators/Numeric.php';

use Core\Event;
use PicoDb\Database;

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
     * @var \PicoDb\Database
     */
    protected $db;

    /**
     * Event dispatcher instance
     *
     * @access protected
     * @var \Core\Event
     */
    protected $event;

    /**
     * Constructor
     *
     * @access public
     * @param  \PicoDb\Database  $db     Database instance
     * @param  \Core\Event       $event  Event dispatcher instance
     */
    public function __construct(Database $db, Event $event)
    {
        $this->db = $db;
        $this->event = $event;
    }
}
