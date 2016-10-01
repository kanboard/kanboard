<?php

namespace Kanboard\Core\Session;

use Kanboard\Core\Base;

/**
 * Session Flash Message
 *
 * @package  session
 * @author   Frederic Guillot
 */
class FlashMessage extends Base
{
    /**
     * Add success message
     *
     * @access public
     * @param  string  $message
     */
    public function success($message)
    {
        $this->setMessage('success', $message);
    }

    /**
     * Add failure message
     *
     * @access public
     * @param  string  $message
     */
    public function failure($message)
    {
        $this->setMessage('failure', $message);
    }

    /**
     * Add new flash message
     *
     * @access public
     * @param  string  $key
     * @param  string  $message
     */
    public function setMessage($key, $message)
    {
        if (! isset($this->sessionStorage->flash)) {
            $this->sessionStorage->flash = array();
        }

        $this->sessionStorage->flash[$key] = $message;
    }

    /**
     * Get flash message
     *
     * @access public
     * @param  string  $key
     * @return string
     */
    public function getMessage($key)
    {
        $message = '';

        if (isset($this->sessionStorage->flash[$key])) {
            $message = $this->sessionStorage->flash[$key];
            unset($this->sessionStorage->flash[$key]);
        }

        return $message;
    }
}
