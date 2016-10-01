<?php

namespace Kanboard\Core\Plugin;

/**
 * Plugin Hooks Handler
 *
 * @package Kanboard\Core\Plugin
 * @author  Frederic Guillot
 */
class Hook
{
    /**
     * List of hooks
     *
     * @access private
     * @var array
     */
    private $hooks = array();

    /**
     * Bind something on a hook
     *
     * @access public
     * @param  string   $hook
     * @param  mixed    $value
     */
    public function on($hook, $value)
    {
        if (! isset($this->hooks[$hook])) {
            $this->hooks[$hook] = array();
        }

        $this->hooks[$hook][] = $value;
    }

    /**
     * Get all bindings for a hook
     *
     * @access public
     * @param  string  $hook
     * @return array
     */
    public function getListeners($hook)
    {
        return isset($this->hooks[$hook]) ? $this->hooks[$hook] : array();
    }

    /**
     * Return true if the hook is used
     *
     * @access public
     * @param  string  $hook
     * @return boolean
     */
    public function exists($hook)
    {
        return isset($this->hooks[$hook]);
    }

    /**
     * Merge listener results with input array
     *
     * @access public
     * @param  string  $hook
     * @param  array   $values
     * @param  array   $params
     * @return array
     */
    public function merge($hook, array &$values, array $params = array())
    {
        foreach ($this->getListeners($hook) as $listener) {
            $result = call_user_func_array($listener, $params);

            if (is_array($result) && ! empty($result)) {
                $values = array_merge($values, $result);
            }
        }

        return $values;
    }

    /**
     * Execute only first listener
     *
     * @access public
     * @param  string  $hook
     * @param  array   $params
     * @return mixed
     */
    public function first($hook, array $params = array())
    {
        foreach ($this->getListeners($hook) as $listener) {
            return call_user_func_array($listener, $params);
        }

        return null;
    }

    /**
     * Hook with reference
     *
     * @access public
     * @param  string $hook
     * @param  mixed  $param
     * @return mixed
     */
    public function reference($hook, &$param)
    {
        foreach ($this->getListeners($hook) as $listener) {
            $listener($param);
        }

        return $param;
    }
}
