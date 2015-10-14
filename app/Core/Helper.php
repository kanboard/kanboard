<?php

namespace Kanboard\Core;

use Pimple\Container;

/**
 * Helper base class
 *
 * @package core
 * @author  Frederic Guillot
 *
 * @property \Helper\App        $app
 * @property \Helper\Asset      $asset
 * @property \Helper\Dt         $dt
 * @property \Helper\File       $file
 * @property \Helper\Form       $form
 * @property \Helper\Subtask    $subtask
 * @property \Helper\Task       $task
 * @property \Helper\Text       $text
 * @property \Helper\Url        $url
 * @property \Helper\User       $user
 */
class Helper
{
    /**
     * Helper instances
     *
     * @access private
     * @var array
     */
    private $helpers = array();

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
     * Load automatically helpers
     *
     * @access public
     * @param  string    $name    Helper name
     * @return mixed
     */
    public function __get($name)
    {
        if (! isset($this->helpers[$name])) {
            $class = '\Kanboard\Helper\\'.ucfirst($name);
            $this->helpers[$name] = new $class($this->container);
        }

        return $this->helpers[$name];
    }

    /**
     * HTML escaping
     *
     * @param  string   $value    Value to escape
     * @return string
     */
    public function e($value)
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8', false);
    }
}
