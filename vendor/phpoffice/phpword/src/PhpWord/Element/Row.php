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

use PhpOffice\PhpWord\Style\Row as RowStyle;

/**
 * Table row element.
 *
 * @since 0.8.0
 */
class Row extends AbstractElement
{
    /**
     * Row height.
     *
     * @var ?int
     */
    private $height;

    /**
     * Row style.
     *
     * @var ?RowStyle
     */
    private $style;

    /**
     * Row cells.
     *
     * @var Cell[]
     */
    private $cells = [];

    /**
     * Create a new table row.
     *
     * @param int $height
     * @param mixed $style
     */
    public function __construct($height = null, $style = null)
    {
        $this->height = $height;
        $this->style = $this->setNewStyle(new RowStyle(), $style, true);
    }

    /**
     * Add a cell.
     *
     * @param int $width
     * @param mixed $style
     *
     * @return Cell
     */
    public function addCell($width = null, $style = null)
    {
        $cell = new Cell($width, $style);
        $cell->setParentContainer($this);
        $this->cells[] = $cell;

        return $cell;
    }

    /**
     * Get all cells.
     *
     * @return Cell[]
     */
    public function getCells()
    {
        return $this->cells;
    }

    /**
     * Get row style.
     *
     * @return ?RowStyle
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * Get row height.
     *
     * @return ?int
     */
    public function getHeight()
    {
        return $this->height;
    }
}
