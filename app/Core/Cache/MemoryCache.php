<?php

namespace Kanboard\Core\Cache;

/**
 * Memory Cache Driver
 *
 * @package  Kanboard\Core\Cache
 * @author   Frederic Guillot
 */
class MemoryCache extends BaseCache
{
    /**
     * Container
     *
     * @access private
     * @var array
     */
    private $storage = array();

    /**
     * Store an item in the cache
     *
     * @access public
     * @param  string  $key
     * @param  mixed   $value
     */
    public function set($key, $value)
    {
        $this->storage[$key] = $value;
    }

    /**
     * Retrieve an item from the cache by key
     *
     * @access public
     * @param  string  $key
     * @return mixed            Null when not found, cached value otherwise
     */
    public function get($key)
    {
        return isset($this->storage[$key]) ? $this->storage[$key] : null;
    }

    /**
     * Clear all cache
     *
     * @access public
     */
    public function flush()
    {
        $this->storage = array();
    }

    /**
     * Remove cached value
     *
     * @access public
     * @param  string  $key
     */
    public function remove($key)
    {
        unset($this->storage[$key]);
    }
}
