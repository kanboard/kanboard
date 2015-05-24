<?php

namespace Core;

/**
 * Helper base class
 *
 * @package core
 * @author  Frederic Guillot
 *
 * @property \Helper\App        $app
 * @property \Helper\Asset      $asset
 * @property \Helper\Datetime   $datetime
 * @property \Helper\File       $file
 * @property \Helper\Form       $form
 * @property \Helper\Subtask    $subtask
 * @property \Helper\Task       $task
 * @property \Helper\Text       $text
 * @property \Helper\Url        $url
 * @property \Helper\User       $user
 */
class Helper extends Base
{
    /**
     * Helper instances
     *
     * @static
     * @access private
     * @var array
     */
    private static $helpers = array();

    /**
     * Load automatically helpers
     *
     * @access public
     * @param  string    $name    Helper name
     * @return mixed
     */
    public function __get($name)
    {
        if (! isset(self::$helpers[$name])) {
            $class = '\Helper\\'.ucfirst($name);
            self::$helpers[$name] = new $class($this->container);
        }

        return self::$helpers[$name];
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
