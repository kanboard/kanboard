<?php

namespace Kanboard\Event;

use Symfony\Component\EventDispatcher\Event as BaseEvent;

/**
 * Authentication Success Event
 *
 * @package  event
 * @author   Frederic Guillot
 */
class AuthSuccessEvent extends BaseEvent
{
    /**
     * Authentication provider name
     *
     * @access private
     * @var string
     */
    private $authType;

    /**
     * Constructor
     *
     * @access public
     * @param  string $authType
     */
    public function __construct($authType)
    {
        $this->authType = $authType;
    }

    /**
     * Get authentication type
     *
     * @return string
     */
    public function getAuthType()
    {
        return $this->authType;
    }
}
