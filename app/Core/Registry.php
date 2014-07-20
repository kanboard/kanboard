<?php

namespace Core;

use RuntimeException;

/**
 * The registry class is a dependency injection container
 *
 * @property mixed db
 * @property mixed event
 * @package core
 * @author  Frederic Guillot
 */
class Registry
{
    /**
     * Contains all dependencies
     *
     * @access private
     * @var array
     */
    private $container = array();

    /**
     * Contains all instances
     *
     * @access private
     * @var array
     */
    private $instances = array();

    /**
     * Set a dependency
     *
     * @access public
     * @param  string   $name   Unique identifier for the service/parameter
     * @param  mixed    $value  The value of the parameter or a closure to define an object
     */
    public function __set($name, $value)
    {
        $this->container[$name] = $value;
    }

    /**
     * Get a dependency
     *
     * @access public
     * @param  string   $name   Unique identifier for the service/parameter
     * @return mixed            The value of the parameter or an object
     * @throws RuntimeException If the identifier is not found
     */
    public function __get($name)
    {
        if (isset($this->container[$name])) {

            if (is_callable($this->container[$name])) {
                return $this->container[$name]();
            }
            else {
                return $this->container[$name];
            }
        }

        throw new \RuntimeException('Identifier not found in the registry: '.$name);
    }

    /**
     * Return a shared instance of a dependency
     *
     * @access public
     * @param  string   $name   Unique identifier for the service/parameter
     * @return mixed            Same object instance of the dependency
     */
    public function shared($name)
    {
        if (! isset($this->instances[$name])) {
            $this->instances[$name] = $this->$name;
        }

        return $this->instances[$name];
    }
}
