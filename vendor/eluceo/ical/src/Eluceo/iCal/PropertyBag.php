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
    protected $elements = array();

    /**
     * Creates a new Property with $name, $value and $params.
     *
     * @param       $name
     * @param       $value
     * @param array $params
     *
     * @return $this
     */
    public function set($name, $value, $params = array())
    {
        $property         = new Property($name, $value, $params);
        $this->elements[] = $property;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return null|Property
     */
    public function get($name)
    {
        // Searching Property in elements-array
        /** @var $property Property */
        foreach ($this->elements as $property) {
            if ($property->getName() == $name) {
                return $property;
            }
        }
    }

    /**
     * Adds a Property. If Property already exists an Exception will be thrown.
     *
     * @param Property $property
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function add(Property $property)
    {
        // Property already exists?
        if (null !== $this->get($property->getName())) {
            throw new \Exception("Property with name '{$property->getName()}' already exists");
        }

        $this->elements[] = $property;

        return $this;
    }

    public function getIterator()
    {
        return new \ArrayObject($this->elements);
    }
}
