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
use Core\Tool;
use Core\Registry;
use PicoDb\Database;

/**
 * Base model class
 *
 * @package  model
 * @author   Frederic Guillot
 *
 * @property \Model\Acl                $acl
 * @property \Model\Action             $action
 * @property \Model\Board              $board
 * @property \Model\Category           $category
 * @property \Model\Comment            $comment
 * @property \Model\Config             $config
 * @property \Model\File               $file
 * @property \Model\LastLogin          $lastLogin
 * @property \Model\Ldap               $ldap
 * @property \Model\Notification       $notification
 * @property \Model\Project            $project
 * @property \Model\SubTask            $subTask
 * @property \Model\Task               $task
 * @property \Model\User               $user
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
     * Registry instance
     *
     * @access protected
     * @var \Core\Registry
     */
    protected $registry;

    /**
     * Constructor
     *
     * @access public
     * @param  \Core\Registry  $registry  Registry instance
     */
    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
        $this->db = $this->registry->shared('db');
        $this->event = $this->registry->shared('event');
    }

    /**
     * Load automatically models
     *
     * @access public
     * @param  string $name Model name
     * @return mixed
     */
    public function __get($name)
    {
        return Tool::loadModel($this->registry, $name);
    }
}
