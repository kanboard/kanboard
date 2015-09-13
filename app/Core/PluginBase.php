<?php

namespace Core;

/**
 * Plugin Base class
 *
 * @package  core
 * @author   Frederic Guillot
 */
abstract class PluginBase extends Base
{
    /**
     * Method called for each request
     *
     * @abstract
     * @access public
     */
    abstract public function initialize();

    /**
     * Returns all classes that needs to be stored in the DI container
     *
     * @access public
     * @return array
     */
    public function getClasses()
    {
        return array();
    }

    /**
     * Listen on internal events
     *
     * @access public
     * @param  string   $event
     * @param  callable $callback
     */
    public function on($event, $callback)
    {
        $container = $this->container;

        $this->container['dispatcher']->addListener($event, function() use ($container, $callback) {
            call_user_func($callback, $container);
        });
    }
}
