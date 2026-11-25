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

use PhpOffice\PhpWord\Style\Table as TableStyle;

/**
 * Table element.
 */
class Table extends AbstractElement
{
    /**
     * Table style.
     *
     * @var ?TableStyle
     */
    private $style;

    /**
     * Table rows.
     *
     * @var Row[]
     */
    private $rows = [];

    /**
     * Table width.
     *
     * @var ?int
     */
    private $width;

    /**
     * Create a new table.
     *
     * @param mixed $style
     */
    public function __construct($style = null)
    {
        $this->style = $this->setNewStyle(new TableStyle(), $style);
    }

    /**
     * Add a row.
     *
     * @param int $height
     * @param mixed $style
     *
     * @return Row
     */
    public function addRow($height = null, $style = null)
    {
        $row = new Row($height, $style);
        $row->setParentContainer($this);
        $this->rows[] = $row;

        return $row;
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
        $index = count($this->rows) - 1;
        $row = $this->rows[$index];
        $cell = $row->addCell($width, $style);

        return $cell;
    }

    /**
     * Get all rows.
     *
     * @return Row[]
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * Get table style.
     *
     * @return null|string|TableStyle
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * Get table width.
     *
     * @return ?int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set table width.
     *
     * @param int $width
     */
    public function setWidth($width): void
    {
        $this->width = $width;
    }

    /**
     * Get column count.
     *
     * @return int
     */
    public function countColumns()
    {
        $columnCount = 0;

        $rowCount = count($this->rows);
        for ($i = 0; $i < $rowCount; ++$i) {
            /** @var Row $row Type hint */
            $row = $this->rows[$i];
            $cellCount = count($row->getCells());
            if ($columnCount < $cellCount) {
                $columnCount = $cellCount;
            }
        }

        return $columnCount;
    }

    /**
     * The first declared cell width for each column.
     *
     * @return int[]
     */
    public function findFirstDefinedCellWidths()
    {
        $cellWidths = [];

        foreach ($this->rows as $row) {
            $cells = $row->getCells();
            if (count($cells) <= count($cellWidths)) {
                continue;
            }
            $cellWidths = [];
            foreach ($cells as $cell) {
                $cellWidths[] = $cell->getWidth();
            }
        }

        return $cellWidths;
    }
}
