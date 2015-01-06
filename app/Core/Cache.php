<?php

namespace Core;

use Pimple\Container;

abstract class Cache
{
    /**
     * Container instance
     *
     * @access protected
     * @var \Pimple\Container
     */
    protected $container;

    abstract public function init();
    abstract public function set($key, $value);
    abstract public function get($key);
    abstract public function flush();
    abstract public function remove($key);

    /**
     * Constructor
     *
     * @access public
     * @param  \Pimple\Container   $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->init();
    }

    /**
     * Proxy cache
     *
     * Note: Arguments must be scalar types
     *
     * @access public
     * @param  string    $container    Container name
     * @param  string    $method       Container method
     * @return mixed
     */
    public function proxy($container, $method)
    {
        $args = func_get_args();
        $key = 'proxy_'.implode('_', $args);
        $result = $this->get($key);

        if ($result === null) {
            $result = call_user_func_array(array($this->container[$container], $method), array_splice($args, 2));
            $this->set($key, $result);
        }

        return $result;
    }
}
