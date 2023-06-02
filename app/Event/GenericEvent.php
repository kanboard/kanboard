<?php

namespace Kanboard\Event;

use ArrayAccess;
use Symfony\Contracts\EventDispatcher\Event as BaseEvent;

class GenericEvent extends BaseEvent implements ArrayAccess
{
    protected $container = array();

    public function __construct(array $values = array())
    {
        $this->container = $values;
    }

    public function getTaskId()
    {
        if (isset($this->container['task']['id'])) {
            return $this->container['task']['id'];
        }

        if (isset($this->container['task_id'])) {
            return $this->container['task_id'];
        }

        return null;
    }

    public function getProjectId()
    {
        if (isset($this->container['task']['project_id'])) {
            return $this->container['task']['project_id'];
        }

        return null;
    }

    public function getAll()
    {
        return $this->container;
    }

    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }
}
