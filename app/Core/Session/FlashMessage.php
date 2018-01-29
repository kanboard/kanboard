<?php

namespace Kanboard\Core\Session;

use Kanboard\Core\Base;

/**
 * Session Flash Message
 *
 * @package  Kanboard\Core\Session
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
        if (! session_exists('flash')) {
            session_set('flash', []);
        }

        session_merge('flash', [$key => $message]);
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

        if (session_exists('flash')) {
            $messages = session_get('flash');

            if (isset($messages[$key])) {
                $message = $messages[$key];
                unset($messages[$key]);
                session_set('flash', $messages);
            }
        }

        return $message;
    }
}
