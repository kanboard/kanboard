<?php

namespace Core;

class MemoryCache extends Cache
{
    private $storage = array();

    public function init()
    {
    }

    public function set($key, $value)
    {
        $this->storage[$key] = $value;
    }

    public function get($key)
    {
        return isset($this->storage[$key]) ? $this->storage[$key] : null;
    }

    public function flush()
    {
        $this->storage = array();
    }

    public function remove($key)
    {
        unset($this->storage[$key]);
    }
}
