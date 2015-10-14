<?php

namespace Kanboard\Auth;

use Pimple\Container;

/**
 * Base auth class
 *
 * @package  auth
 * @author   Frederic Guillot
 */
abstract class Base extends \Kanboard\Core\Base
{
    /**
     * Database instance
     *
     * @access protected
     * @var \PicoDb\Database
     */
    protected $db;

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
}
