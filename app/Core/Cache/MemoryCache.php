<?php

namespace Kanboard\Core\Cache;

/**
 * Memory Cache
 *
 * @package  cache
 * @author   Frederic Guillot
 */
class MemoryCache extends Base implements CacheInterface
{
    /**
     * Container
     *
     * @access private
     * @var array
     */
    private $storage = array();

    /**
     * Save a new value in the cache
     *
     * @access public
     * @param  string  $key
     * @param  string  $value
     */
    public function set($key, $value)
    {
        $this->storage[$key] = $value;
    }

    /**
     * Fetch value from cache
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
