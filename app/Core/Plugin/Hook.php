<?php

namespace Core\Plugin;

/**
 * Plugin Hooks Handler
 *
 * @package  plugin
 * @author   Frederic Guillot
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
}
