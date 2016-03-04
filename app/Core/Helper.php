<?php

namespace Kanboard\Core;

use Pimple\Container;

/**
 * Helper base class
 *
 * @package core
 * @author  Frederic Guillot
 *
 * @property \Kanboard\Helper\App        $app
 * @property \Kanboard\Helper\Asset      $asset
 * @property \Kanboard\Helper\Dt         $dt
 * @property \Kanboard\Helper\File       $file
 * @property \Kanboard\Helper\Form       $form
 * @property \Kanboard\Helper\Subtask    $subtask
 * @property \Kanboard\Helper\Task       $task
 * @property \Kanboard\Helper\Text       $text
 * @property \Kanboard\Helper\Url        $url
 * @property \Kanboard\Helper\User       $user
 * @property \Kanboard\Helper\Layout     $layout
 * @property \Kanboard\Helper\Model      $model
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
