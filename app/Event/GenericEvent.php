<?php

namespace Event;

use ArrayAccess;
use Symfony\Component\EventDispatcher\Event as BaseEvent;

class GenericEvent extends BaseEvent implements ArrayAccess
{
    private $container = array();

    public function __construct(array $values = array())
    {
        $this->container = $values;
    }

    public function getAll()
    {
        return $this->container;
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }
}
