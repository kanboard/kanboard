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

use Eluceo\iCal\Property\ArrayValue;
use Eluceo\iCal\Property\StringValue;
use Eluceo\iCal\Property\ValueInterface;

/**
 * The Property Class represents a property as defined in RFC 2445.
 *
 * The content of a line (unfolded) will be rendered in this class
 */
class Property
{
    /**
     * The value of the Property.
     *
     * @var ValueInterface
     */
    protected $value;

    /**
     * The params of the Property.
     *
     * @var ParameterBag
     */
    protected $parameterBag;

    /**
     * @var string
     */
    protected $name;

    /**
     * @param       $name
     * @param       $value
     * @param array $params
     */
    public function __construct($name, $value, $params = array())
    {
        $this->name = $name;
        $this->setValue($value);
        $this->parameterBag = new ParameterBag($params);
    }

    /**
     * Renders an unfolded line.
     *
     * @return string
     */
    public function toLine()
    {
        // Property-name
        $line = $this->getName();

        // Adding params
        //@todo added check for $this->parameterBag because doctrine/orm proxies won't execute constructor - ok?
        if ($this->parameterBag && $this->parameterBag->hasParams()) {
            $line .= ';' . $this->parameterBag->toString();
        }

        // Property value
        $line .= ':' . $this->value->getEscapedValue();

        return $line;
    }

    /**
     * Get all unfolded lines.
     *
     * @return array
     */
    public function toLines()
    {
        return array($this->toLine());
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     */
    public function setParam($name, $value)
    {
        $this->parameterBag->setParam($name, $value);

        return $this;
    }

    /**
     * @param $name
     */
    public function getParam($name)
    {
        return $this->parameterBag->getParam($name);
    }

    /**
     * @param mixed $value
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function setValue($value)
    {
        if (is_scalar($value)) {
            $this->value = new StringValue($value);
        } elseif (is_array($value)) {
            $this->value = new ArrayValue($value);
        } else {
            if (!$value instanceof ValueInterface) {
                throw new \Exception('The value must implement the ValueInterface.');
            } else {
                $this->value = $value;
            }
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
