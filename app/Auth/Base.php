<?php

namespace Auth;

use Core\Tool;
use Pimple\Container;

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
     * Container instance
     *
     * @access protected
     * @var \Pimple\Container
     */
    protected $container;

    /**
     * Constructor
     *
     * @access public
     * @param  \Pimple\Container    $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->db = $this->container['db'];
    }

    /**
     * Load automatically models
     *
     * @access public
     * @param  string     $name    Model name
     * @return mixed
     */
    public function __get($name)
    {
        return Tool::loadModel($this->container, $name);
    }
}
