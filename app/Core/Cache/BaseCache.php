<?php

namespace Kanboard\Core\Cache;

/**
 * Base Class for Cache Drivers
 *
 * @package  Kanboard\Core\Cache
 * @author   Frederic Guillot
 */
abstract class BaseCache
{
    /**
     * Store an item in the cache
     *
     * @access public
     * @param  string  $key
     * @param  string  $value
     */
    abstract public function set($key, $value);

    /**
     * Retrieve an item from the cache by key
     *
     * @access public
     * @param  string  $key
     * @return mixed            Null when not found, cached value otherwise
     */
    abstract public function get($key);

    /**
     * Remove all items from the cache
     *
     * @access public
     */
    abstract public function flush();

    /**
     * Remove an item from the cache
     *
     * @access public
     * @param  string  $key
     */
    abstract public function remove($key);

    /**
     * Proxy cache
     *
     * Note: Arguments must be scalar types
     *
     * @access public
     * @param  string    $class        Class instance
     * @param  string    $method       Container method
     * @return mixed
     */
    public function proxy($class, $method)
    {
        $args = func_get_args();
        array_shift($args);

        $key = 'proxy:'.get_class($class).':'.implode(':', $args);
        $result = $this->get($key);

        if ($result === null) {
            $result = call_user_func_array(array($class, $method), array_splice($args, 1));
            $this->set($key, $result);
        }

        return $result;
    }
}
