<?php

namespace Event;

use Symfony\Component\EventDispatcher\Event as BaseEvent;

class AuthEvent extends BaseEvent
{
    private $auth_name;
    private $user_id;

    public function __construct($auth_name, $user_id)
    {
        $this->auth_name = $auth_name;
        $this->user_id = $user_id;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function getAuthType()
    {
        return $this->auth_name;
    }
}
