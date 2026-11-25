<?php

/**
 * This file is part of PHPWord - A pure PHP library for reading and writing
 * word processing documents.
 *
 * PHPWord is free software distributed under the terms of the GNU Lesser
 * General Public License version 3 as published by the Free Software Foundation.
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code. For the full list of
 * contributors, visit https://github.com/PHPOffice/PHPWord/contributors.
 *
 * @see         https://github.com/PHPOffice/PHPWord
 *
 * @license     http://www.gnu.org/licenses/lgpl.txt LGPL version 3
 */

namespace PhpOffice\PhpWord\Style;

use InvalidArgumentException;
use PhpOffice\PhpWord\Shared\Text;

/**
 * Abstract style class.
 *
 * @since 0.10.0
 */
abstract class AbstractStyle
{
    /**
     * Style name.
     *
     * @var ?string
     */
    protected $styleName;

    /**
     * Index number in Style collection for named style.
     *
     * This number starts from one and defined in Style::setStyleValues()
     *
     * @var null|int
     */
    protected $index;

    /**
     * Aliases.
     *
     * @var array
     */
    protected $aliases = [];

    /**
     * Is this an automatic style? (Used primarily in OpenDocument driver).
     *
     * @var bool
     *
     * @since 0.11.0
     */
    private $isAuto = false;

    /**
     * Get style name.
     *
     * @return ?string
     */
    public function getStyleName()
    {
        return $this->styleName;
    }

    /**
     * Set style name.
     *
     * @param string $value
     *
     * @return self
     */
    public function setStyleName($value)
    {
        $this->styleName = $value;

        return $this;
    }

    /**
     * Get index number.
     *
     * @return null|int
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * Set index number.
     *
     * @param null|int $value
     *
     * @return self
     */
    public function setIndex($value = null)
    {
        $this->index = $this->setIntVal($value, $this->index);

        return $this;
    }

    /**
     * Get is automatic style flag.
     *
     * @return bool
     */
    public function isAuto()
    {
        return $this->isAuto;
    }

    /**
     * Set is automatic style flag.
     *
     * @param bool $value
     *
     * @return self
     */
    public function setAuto($value = true)
    {
        $this->isAuto = $this->setBoolVal($value, $this->isAuto);

        return $this;
    }

    /**
     * Return style value of child style object, e.g. `left` from `Indentation` child style of `Paragraph`.
     *
     * @param AbstractStyle $substyleObject
     * @param string $substyleProperty
     *
     * @return mixed
     *
     * @since 0.12.0
     */
    public function getChildStyleValue($substyleObject, $substyleProperty)
    {
        if ($substyleObject !== null) {
            $method = "get{$substyleProperty}";

            return $substyleObject->$method();
        }

        return null;
    }

    /**
     * Set style value template method.
     *
     * Some child classes have their own specific overrides.
     * Backward compability check for versions < 0.10.0 which use underscore
     * prefix for their private properties.
     * Check if the set method is exists. Throws an exception?
     *
     * @param string $key
     * @param array|int|string $value
     *
     * @return self
     */
    public function setStyleValue($key, $value)
    {
        if (isset($this->aliases[$key])) {
            $key = $this->aliases[$key];
        }

        if ($key === 'align') {
            $key = 'alignment';
        }

        $method = 'set' . Text::removeUnderscorePrefix($key);
        if (method_exists($this, $method)) {
            $this->$method($value);
        }

        return $this;
    }

    /**
     * Set style by using associative array.
     *
     * @param array $values
     *
     * @return self
     */
    public function setStyleByArray($values = [])
    {
        foreach ($values as $key => $value) {
            $this->setStyleValue($key, $value);
        }

        return $this;
    }

    /**
     * Set default for null and empty value.
     *
     * @param ?string $value
     * @param string $default
     *
     * @return string
     */
    protected function setNonEmptyVal($value, $default)
    {
        if ($value === null || $value == '') {
            $value = $default;
        }

        return $value;
    }

    /**
     * Set bool value.
     *
     * @param bool $value
     * @param bool $default
     *
     * @return bool
     */
    protected function setBoolVal($value, $default)
    {
        if (!is_bool($value)) {
            $value = $default;
        }

        return $value;
    }

    /**
     * Set numeric value.
     *
     * @param mixed $value
     * @param null|float|int $default
     *
     * @return null|float|int
     */
    protected function setNumericVal($value, $default = null)
    {
        if (!is_numeric($value)) {
            $value = $default;
        }

        return $value;
    }

    /**
     * Set integer value: Convert string that contains only numeric into integer.
     *
     * @param null|float|int|string $value
     * @param null|int $default
     *
     * @return null|int
     */
    protected function setIntVal($value, $default = null)
    {
        if (is_string($value) && (preg_match('/[^\d]/', $value) == 0)) {
            $value = (int) $value;
        }
        if (!is_numeric($value)) {
            $value = $default;
        } else {
            $value = (int) $value;
        }

        return $value;
    }

    /**
     * Set float value: Convert string that contains only numeric into float.
     *
     * @param mixed $value
     * @param null|float $default
     *
     * @return null|float
     */
    protected function setFloatVal($value, $default = null)
    {
        if (is_string($value) && (preg_match('/[^\d\.\,]/', $value) == 0)) {
            $value = (float) $value;
        }
        if (!is_numeric($value)) {
            $value = $default;
        }

        return $value;
    }

    /**
     * Set enum value.
     *
     * @param mixed $value
     * @param array $enum
     * @param mixed $default
     *
     * @return mixed
     */
    protected function setEnumVal($value = null, $enum = [], $default = null)
    {
        if ($value != null && trim($value) != '' && !empty($enum) && !in_array($value, $enum)) {
            throw new InvalidArgumentException("Invalid style value: {$value} Options:" . implode(',', $enum));
        } elseif ($value === null || trim($value) == '') {
            $value = $default;
        }

        return $value;
    }

    /**
     * Set object value.
     *
     * @param mixed $value
     * @param mixed &$style
     *
     * @return mixed
     */
    protected function setObjectVal($value, string $styleName, &$style)
    {
        $styleClass = substr(static::class, 0, (int) strrpos(static::class, '\\')) . '\\' . $styleName;
        if (is_array($value)) {
            /** @var AbstractStyle $style Type hint */
            if (!$style instanceof $styleClass) {
                $style = new $styleClass();
            }
            $style->setStyleByArray($value);
        } else {
            $style = $value;
        }

        return $style;
    }

    /**
     * Set $property value and set $pairProperty = false when $value = true.
     *
     * @param bool &$property
     * @param bool &$pairProperty
     * @param bool $value
     *
     * @return self
     */
    protected function setPairedVal(&$property, &$pairProperty, $value)
    {
        $property = $this->setBoolVal($value, $property);
        if ($value === true) {
            $pairProperty = false;
        }

        return $this;
    }
}
