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
 * Structured document tag (SDT) element.
 *
 * @since 0.12.0
 */
class SDT extends Text
{
    /**
     * Form field type: comboBox|dropDownList|date.
     *
     * @var string
     */
    private $type;

    /**
     * Value.
     *
     * @var null|bool|int|string
     */
    private $value;

    /**
     * CheckBox/DropDown list entries.
     *
     * @var array
     */
    private $listItems = [];

    /**
     * Alias.
     *
     * @var string
     */
    private $alias;

    /**
     * Tag.
     *
     * @var string
     */
    private $tag;

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
        $enum = ['plainText', 'comboBox', 'dropDownList', 'date'];
        $this->type = $this->setEnumVal($value, $enum, 'comboBox');

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
     * Get listItems.
     *
     * @return array
     */
    public function getListItems()
    {
        return $this->listItems;
    }

    /**
     * Set listItems.
     *
     * @param array $value
     *
     * @return self
     */
    public function setListItems($value)
    {
        $this->listItems = $value;

        return $this;
    }

    /**
     * Get tag.
     *
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set tag.
     *
     * @param string $tag
     *
     * @return self
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get alias.
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Set alias.
     *
     * @param string $alias
     *
     * @return self
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }
}
