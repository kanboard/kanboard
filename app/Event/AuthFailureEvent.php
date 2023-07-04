<?php

namespace Kanboard\Event;

use Symfony\Contracts\EventDispatcher\Event as BaseEvent;

/**
 * Authentication Failure Event
 *
 * @package  event
 * @author   Frederic Guillot
 */
class AuthFailureEvent extends BaseEvent
{
    /**
     * Username
     *
     * @access private
     * @var string
     */
    private $username = '';

    /**
     * Constructor
     *
     * @access public
     * @param  string $username
     */
    public function __construct($username = '')
    {
        $this->username = $username;
    }

    /**
     * Get username
     *
     * @access public
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }
}
