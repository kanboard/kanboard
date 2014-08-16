<?php

namespace Auth;

use Core\Tool;
use Core\Registry;

/**
 * Base auth class
 *
 * @package  auth
 * @author   Frederic Guillot
 *
 * @property \Model\Acl                $acl
 * @property \Model\LastLogin          $lastLogin
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
