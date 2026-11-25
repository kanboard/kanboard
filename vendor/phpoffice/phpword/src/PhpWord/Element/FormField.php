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

namespace PhpOffice\PhpWord\Element;

/**
 * Form field element.
 *
 * @since 0.12.0
 * @see  http://www.datypic.com/sc/ooxml/t-w_CT_FFData.html
 */
class FormField extends Text
{
    /**
     * Form field type: textinput|checkbox|dropdown.
     *
     * @var string
     */
    private $type = 'textinput';

    /**
     * Form field name.
     *
     * @var ?string
     */
    private $name;

    /**
     * Default value.
     *
     * - TextInput: string
     * - CheckBox: bool
     * - DropDown: int Index of entries (zero based)
     *
     * @var bool|int|string
     */
    private $default;

    /**
     * Value.
     *
     * @var null|bool|int|string
     */
    private $value;

    /**
     * Dropdown entries.
     *
     * @var array
     */
    private $entries = [];

    /**
     * Create new instance.
     *
     * @param string $type
     * @param mixed $fontStyle
     * @param mixed $paragraphStyle
     */
    public function __construct($type, $fontStyle = null, $paragraphStyle = null)
    {
        parent::__construct(null, $fontStyle, $paragraphStyle);
        $this->setType($type);
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type.
     *
     * @param string $value
     *
     * @return self
     */
    public function setType($value)
    {
        $enum = ['textinput', 'checkbox', 'dropdown'];
        $this->type = $this->setEnumVal($value, $enum, $this->type);

        return $this;
    }

    /**
     * Get name.
     *
     * @return ?string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name.
     *
     * @param ?string $value
     *
     * @return self
     */
    public function setName($value)
    {
        $this->name = $value;

        return $this;
    }

    /**
     * Get default.
     *
     * @return bool|int|string
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * Set default.
     *
     * @param bool|int|string $value
     *
     * @return self
     */
    public function setDefault($value)
    {
        $this->default = $value;

        return $this;
    }

    /**
     * Get value.
     *
     * @return null|bool|int|string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set value.
     *
     * @param null|bool|int|string $value
     *
     * @return self
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get entries.
     *
     * @return array
     */
    public function getEntries()
    {
        return $this->entries;
    }

    /**
     * Set entries.
     *
     * @param array $value
     *
     * @return self
     */
    public function setEntries($value)
    {
        $this->entries = $value;

        return $this;
    }
}
