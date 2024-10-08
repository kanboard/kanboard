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

    #[\ReturnTypeWillChange]
    public function rewind() {
        $this->position = 0;
    }

    /**
     * @return BaseEventBuilder
     */
    #[\ReturnTypeWillChange]
    public function current() {
        return $this->builders[$this->position];
    }

    #[\ReturnTypeWillChange]
    public function key() {
        return $this->position;
    }

    #[\ReturnTypeWillChange]
    public function next() {
        ++$this->position;
    }

    #[\ReturnTypeWillChange]
    public function valid() {
        return isset($this->builders[$this->position]);
    }
}
