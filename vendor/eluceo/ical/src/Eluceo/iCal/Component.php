<?php

/*
 * This file is part of the eluceo/iCal package.
 *
 * (c) Markus Poerschke <markus@eluceo.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eluceo\iCal;
use Eluceo\iCal\Util\ComponentUtil;

/**
 * Abstract Calender Component.
 */
abstract class Component
{
    /**
     * Array of Components.
     *
     * @var Component[]
     */
    protected $components = array();

    /**
     * The type of the concrete Component.
     *
     * @abstract
     *
     * @return string
     */
    abstract public function getType();

    /**
     * Adds a Component.
     *
     * If $key is given, the component at $key will be replaced else the component will be append.
     *
     * @param Component $component The Component that will be added
     * @param null      $key       The key of the Component
     */
    public function addComponent(Component $component, $key = null)
    {
        if (null == $key) {
            $this->components[] = $component;
        } else {
            $this->components[$key] = $component;
        }
    }

    /**
     * Renders an array containing the lines of the iCal file.
     *
     * @return array
     */
    public function build()
    {
        $lines = array();

        $lines[] = sprintf('BEGIN:%s', $this->getType());

        /** @var $property Property */
        foreach ($this->buildPropertyBag() as $property) {
            foreach ($property->toLines() as $l) {
                $lines[] = $l;
            }
        }

        /** @var $component Component */
        foreach ($this->components as $component) {
            foreach ($component->build() as $l) {
                $lines[] = $l;
            }
        }

        $lines[] = sprintf('END:%s', $this->getType());

        $ret = array();

        foreach ($lines as $line) {
            foreach (ComponentUtil::fold($line) as $l) {
                $ret[] = $l;
            }
        }

        return $ret;
    }

    /**
     * Renders the output.
     *
     * @return string
     */
    public function render()
    {
        return implode("\r\n", $this->build());
    }

    /**
     * Renders the output when treating the class as a string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * Building the PropertyBag.
     *
     * @abstract
     * @return PropertyBag
     */
    abstract public function buildPropertyBag();
}
