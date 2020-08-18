<?php

/*
 * This file is part of the eluceo/iCal package.
 *
 * (c) Markus Poerschke <markus@eluceo.de>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Eluceo\iCal;

class PropertyBag implements \IteratorAggregate
{
    /**
     * @var array
     */
    protected $elements = [];

    /**
     * Creates a new Property with $name, $value and $params.
     *
     * @param       $name
     * @param       $value
     * @param array $params
     *
     * @return $this
     */
    public function set($name, $value, $params = [])
    {
        $this->add(new Property($name, $value, $params));

        return $this;
    }

    /**
     * @return Property|null
     */
    public function get(string $name)
    {
        if (isset($this->elements[$name])) {
            return $this->elements[$name];
        }

        return null;
    }

    /**
     * Adds a Property. If Property already exists an Exception will be thrown.
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function add(Property $property)
    {
        $name = $property->getName();

        if (isset($this->elements[$name])) {
            throw new \Exception("Property with name '{$name}' already exists");
        }

        $this->elements[$name] = $property;

        return $this;
    }

    public function getIterator()
    {
        return new \ArrayObject($this->elements);
    }
}
