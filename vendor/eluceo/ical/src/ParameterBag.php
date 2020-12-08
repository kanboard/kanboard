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

class ParameterBag
{
    /**
     * The params.
     *
     * @var array
     */
    protected $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function setParam($name, $value)
    {
        $this->params[$name] = $value;
    }

    /**
     * @param $name
     *
     * @return array|mixed
     */
    public function getParam($name)
    {
        if (isset($this->params[$name])) {
            return $this->params[$name];
        }

        return null;
    }

    /**
     * Checks if there are any params.
     */
    public function hasParams(): bool
    {
        return count($this->params) > 0;
    }

    public function toString(): string
    {
        $line = '';
        foreach ($this->params as $param => $paramValues) {
            if (!is_array($paramValues)) {
                $paramValues = [$paramValues];
            }
            foreach ($paramValues as $k => $v) {
                $paramValues[$k] = $this->escapeParamValue($v);
            }

            if ('' != $line) {
                $line .= ';';
            }

            $line .= $param . '=' . implode(',', $paramValues);
        }

        return $line;
    }

    /**
     * Returns an escaped string for a param value.
     *
     * @param string $value
     *
     * @return string
     */
    private function escapeParamValue($value)
    {
        $count = 0;
        $value = str_replace('\\', '\\\\', $value);
        $value = str_replace('"', '\"', $value, $count);
        $value = str_replace("\n", '\\n', $value);
        if (false !== strpos($value, ';') || false !== strpos($value, ',') || false !== strpos($value, ':') || $count) {
            $value = '"' . $value . '"';
        }

        return $value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }
}
