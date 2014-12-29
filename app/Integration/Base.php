<?php

namespace Integration;

use Pimple\Container;

/**
 * Base class
 *
 * @package  integration
 * @author   Frederic Guillot
 *
 * @property \Model\Task                   $task
 * @property \Model\TaskFinder             $taskFinder
 * @property \Model\User                   $user
 */
abstract class Base
{
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
     * @param  \Pimple\Container   $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Load automatically class from the container
     *
     * @access public
     * @param  string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->container[$name];
    }
}
