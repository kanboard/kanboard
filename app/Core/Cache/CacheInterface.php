<?php

namespace Kanboard\Core\Cache;

/**
 * Cache Interface
 *
 * @package  cache
 * @author   Frederic Guillot
 */
interface CacheInterface
{
    /**
     * Save a new value in the cache
     *
     * @access public
     * @param  string  $key
     * @param  string  $value
     */
    public function set($key, $value);

    /**
     * Fetch value from cache
     *
     * @access public
     * @param  string  $key
     * @return mixed            Null when not found, cached value otherwise
     */
    public function get($key);

    /**
     * Clear all cache
     *
     * @access public
     */
    public function flush();

    /**
     * Remove cached value
     *
     * @access public
     * @param  string  $key
     */
    public function remove($key);
}
