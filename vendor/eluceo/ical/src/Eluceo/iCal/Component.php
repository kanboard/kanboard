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
     * The order in which the components will be rendered during build.
     *
     * Not defined components will be appended at the end.
     *
     * @var array
     */
    private $componentsBuildOrder = array('VTIMEZONE', 'DAYLIGHT', 'STANDARD');

    /**
     * The type of the concrete Component.
     *
     * @abstract
     *
     * @return string
     */
    abstract public function getType();

    /**
     * Building the PropertyBag.
     *
     * @abstract
     *
     * @return PropertyBag
     */
    abstract public function buildPropertyBag();

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

        $this->buildComponents($lines);

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
     * @param $lines
     *
     * @return array
     */
    private function buildComponents(array &$lines)
    {
        $componentsByType = array();

        /** @var $component Component */
        foreach ($this->components as $component) {
            $type = $component->getType();
            if (!isset($componentsByType[$type])) {
                $componentsByType[$type] = array();
            }
            $componentsByType[$type][] = $component;
        }

        // render ordered components
        foreach ($this->componentsBuildOrder as $type) {
            if (!isset($componentsByType[$type])) {
                continue;
            }
            foreach ($componentsByType[$type] as $component) {
                $this->addComponentLines($lines, $component);
            }
            unset($componentsByType[$type]);
        }

        // render all other
        foreach ($componentsByType as $components) {
            foreach ($components as $component) {
                $this->addComponentLines($lines, $component);
            }
        }
    }

    /**
     * @param array     $lines
     * @param Component $component
     */
    private function addComponentLines(array &$lines, Component $component)
    {
        foreach ($component->build() as $l) {
            $lines[] = $l;
        }
    }
}
