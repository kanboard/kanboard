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

use PhpOffice\PhpWord\Style\Shape as ShapeStyle;

/**
 * Shape element.
 *
 * @since 0.12.0
 */
class Shape extends AbstractElement
{
    /**
     * Shape type arc|curve|line|polyline|rect|oval.
     *
     * @var string
     */
    private $type;

    /**
     * Shape style.
     *
     * @var ?ShapeStyle
     */
    private $style;

    /**
     * Create new instance.
     *
     * @param string $type
     * @param mixed $style
     */
    public function __construct($type, $style = null)
    {
        $this->setType($type);
        $this->style = $this->setNewStyle(new ShapeStyle(), $style);
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
     * Set pattern.
     *
     * @param string $value
     *
     * @return self
     */
    public function setType($value = null)
    {
        $enum = ['arc', 'curve', 'line', 'polyline', 'rect', 'oval'];
        $this->type = $this->setEnumVal($value, $enum, null);

        return $this;
    }

    /**
     * Get shape style.
     *
     * @return ?ShapeStyle
     */
    public function getStyle()
    {
        return $this->style;
    }
}
