<?php

namespace Kanboard\EventBuilder;

use Iterator;

/**
 * Class EventIteratorBuilder
 *
 * @package Kanboard\EventBuilder
 * @author  Frederic Guillot
 */
class EventIteratorBuilder implements Iterator {
    private $position = 0;
    private $builders = array();

    /**
     * Set builder
     *
     * @access public
     * @param  BaseEventBuilder $builder
     * @return $this
     */
    public function withBuilder(BaseEventBuilder $builder)
    {
        $this->builders[] = $builder;
        return $this;
    }

    public function rewind() {
        $this->position = 0;
    }

    /**
     * @return BaseEventBuilder
     */
    public function current() {
        return $this->builders[$this->position];
    }

    public function key() {
        return $this->position;
    }

    public function next() {
        ++$this->position;
    }

    public function valid() {
        return isset($this->builders[$this->position]);
    }
}
