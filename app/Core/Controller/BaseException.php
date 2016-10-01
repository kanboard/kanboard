<?php

namespace Kanboard\Core\Controller;

use Exception;

/**
 * Class AccessForbiddenException
 *
 * @package Kanboard\Core\Controller
 * @author  Frederic Guillot
 */
class BaseException extends Exception
{
    protected $withoutLayout = false;

    /**
     * Get object instance
     *
     * @static
     * @access public
     * @param  string $message
     * @return static
     */
    public static function getInstance($message = '')
    {
        return new static($message);
    }

    /**
     * There is no layout
     *
     * @access public
     * @return BaseException
     */
    public function withoutLayout()
    {
        $this->withoutLayout = true;
        return $this;
    }

    /**
     * Return true if no layout
     *
     * @access public
     * @return boolean
     */
    public function hasLayout()
    {
        return $this->withoutLayout;
    }
}
