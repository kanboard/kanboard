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

use PhpOffice\PhpWord\ComplexType\TblWidth as TblWidthComplexType;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\SimpleType\JcTable;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\Style;

class Table extends Border
{
    //values for http://www.datypic.com/sc/ooxml/t-w_ST_TblLayoutType.html
    /**
     * AutoFit Table Layout.
     *
     * @var string
     */
    const LAYOUT_AUTO = 'autofit';
    /**
     * Fixed Width Table Layout.
     *
     * @var string
     */
    const LAYOUT_FIXED = 'fixed';

    /**
     * Is this a first row style?
     *
     * @var bool
     */
    private $isFirstRow = false;

    /**
     * Style for first row.
     *
     * @var Table
     */
    private $firstRowStyle;

    /**
     * Cell margin top.
     *
     * @var int
     */
    private $cellMarginTop;

    /**
     * Cell margin left.
     *
     * @var int
     */
    private $cellMarginLeft;

    /**
     * Cell margin right.
     *
     * @var int
     */
    private $cellMarginRight;

    /**
     * Cell margin bottom.
     *
     * @var int
     */
    private $cellMarginBottom;

    /**
     * Border size inside horizontal.
     *
     * @var int
     */
    private $borderInsideHSize;

    /**
     * Border color inside horizontal.
     *
     * @var string
     */
    private $borderInsideHColor;

    /**
     * Border size inside vertical.
     *
     * @var int
     */
    private $borderInsideVSize;

    /**
     * Border color inside vertical.
     *
     * @var string
     */
    private $borderInsideVColor;

    /**
     * Shading.
     *
     * @var Shading
     */
    private $shading;

    /**
     * @var string
     */
    private $alignment = '';

    /**
     * @var float|int Width value
     */
    private $width = 0;

    /**
     * @var string Width unit
     */
    private $unit = TblWidth::AUTO;

    /**
     * @var null|float|int cell spacing value
     */
    protected $cellSpacing;

    /**
     * @var string Table Layout
     */
    private $layout = self::LAYOUT_AUTO;

    /**
     * Position.
     *
     * @var ?TablePosition
     */
    private $position;

    /** @var null|TblWidthComplexType */
    private $indent;

    /**
     * The width of each column, computed based on the max cell width of each column.
     *
     * @var int[]
     */
    private $columnWidths;

    /**
     * Visually Right to Left Table.
     *
     * @see  http://www.datypic.com/sc/ooxml/e-w_bidiVisual-1.html
     *
     * @var ?bool
     */
    private $bidiVisual;

    /**
     * Create new table style.
     *
     * @param mixed $tableStyle
     * @param mixed $firstRowStyle
     */
    public function __construct($tableStyle = null, $firstRowStyle = null)
    {
        // Clone first row from table style, but with certain properties disabled
        if ($firstRowStyle !== null && is_array($firstRowStyle)) {
            $this->firstRowStyle = clone $this;
            $this->firstRowStyle->isFirstRow = true;
            unset($this->firstRowStyle->firstRowStyle, $this->firstRowStyle->borderInsideHSize, $this->firstRowStyle->borderInsideHColor, $this->firstRowStyle->borderInsideVSize, $this->firstRowStyle->borderInsideVColor, $this->firstRowStyle->cellMarginTop, $this->firstRowStyle->cellMarginLeft, $this->firstRowStyle->cellMarginRight, $this->firstRowStyle->cellMarginBottom, $this->firstRowStyle->cellSpacing);
            $this->firstRowStyle->setStyleByArray($firstRowStyle);
        }

        if ($tableStyle !== null && is_array($tableStyle)) {
            $this->setStyleByArray($tableStyle);
        }
    }

    /**
     * @param null|float|int $cellSpacing
     */
    public function setCellSpacing($cellSpacing = null): void
    {
        $this->cellSpacing = $cellSpacing;
    }

    /**
     * @return null|float|int
     */
    public function getCellSpacing()
    {
        return $this->cellSpacing;
    }

    /**
     * Set first row.
     *
     * @return Table
     */
    public function getFirstRow()
    {
        return $this->firstRowStyle;
    }

    /**
     * Get background.
     *
     * @return ?string
     */
    public function getBgColor()
    {
        if ($this->shading !== null) {
            return $this->shading->getFill();
        }

        return null;
    }

    /**
     * Set background.
     *
     * @param string $value
     *
     * @return self
     */
    public function setBgColor($value = null)
    {
        $this->setShading(['fill' => $value]);

        return $this;
    }

    /**
     * Get TLRBHV Border Size.
     *
     * @return int[]
     */
    public function getBorderSize()
    {
        return [
            $this->getBorderTopSize(),
            $this->getBorderLeftSize(),
            $this->getBorderRightSize(),
            $this->getBorderBottomSize(),
            $this->getBorderInsideHSize(),
            $this->getBorderInsideVSize(),
        ];
    }

    /**
     * Set TLRBHV Border Size.
     *
     * @param int $value Border size in eighths of a point (1/8 point)
     *
     * @return self
     */
    public function setBorderSize($value = null)
    {
        $this->setBorderTopSize($value);
        $this->setBorderLeftSize($value);
        $this->setBorderRightSize($value);
        $this->setBorderBottomSize($value);
        $this->setBorderInsideHSize($value);
        $this->setBorderInsideVSize($value);

        return $this;
    }

    /**
     * Get TLRBHV Border Color.
     *
     * @return string[]
     */
    public function getBorderColor()
    {
        return [
            $this->getBorderTopColor(),
            $this->getBorderLeftColor(),
            $this->getBorderRightColor(),
            $this->getBorderBottomColor(),
            $this->getBorderInsideHColor(),
            $this->getBorderInsideVColor(),
        ];
    }

    /**
     * Set TLRBHV Border Color.
     *
     * @param string $value
     *
     * @return self
     */
    public function setBorderColor($value = null)
    {
        $this->setBorderTopColor($value);
        $this->setBorderLeftColor($value);
        $this->setBorderRightColor($value);
        $this->setBorderBottomColor($value);
        $this->setBorderInsideHColor($value);
        $this->setBorderInsideVColor($value);

        return $this;
    }

    /**
     * Get border size inside horizontal.
     *
     * @return int
     */
    public function getBorderInsideHSize()
    {
        return $this->getTableOnlyProperty('borderInsideHSize');
    }

    /**
     * Set border size inside horizontal.
     *
     * @param int $value
     *
     * @return self
     */
    public function setBorderInsideHSize($value = null)
    {
        return $this->setTableOnlyProperty('borderInsideHSize', $value);
    }

    /**
     * Get border color inside horizontal.
     *
     * @return string
     */
    public function getBorderInsideHColor()
    {
        return $this->getTableOnlyProperty('borderInsideHColor');
    }

    /**
     * Set border color inside horizontal.
     *
     * @param string $value
     *
     * @return self
     */
    public function setBorderInsideHColor($value = null)
    {
        return $this->setTableOnlyProperty('borderInsideHColor', $value, false);
    }

    /**
     * Get border size inside vertical.
     *
     * @return int
     */
    public function getBorderInsideVSize()
    {
        return $this->getTableOnlyProperty('borderInsideVSize');
    }

    /**
     * Set border size inside vertical.
     *
     * @param int $value
     *
     * @return self
     */
    public function setBorderInsideVSize($value = null)
    {
        return $this->setTableOnlyProperty('borderInsideVSize', $value);
    }

    /**
     * Get border color inside vertical.
     *
     * @return string
     */
    public function getBorderInsideVColor()
    {
        return $this->getTableOnlyProperty('borderInsideVColor');
    }

    /**
     * Set border color inside vertical.
     *
     * @param string $value
     *
     * @return self
     */
    public function setBorderInsideVColor($value = null)
    {
        return $this->setTableOnlyProperty('borderInsideVColor', $value, false);
    }

    /**
     * Get cell margin top.
     *
     * @return int
     */
    public function getCellMarginTop()
    {
        return $this->getTableOnlyProperty('cellMarginTop');
    }

    /**
     * Set cell margin top.
     *
     * @param int $value
     *
     * @return self
     */
    public function setCellMarginTop($value = null)
    {
        return $this->setTableOnlyProperty('cellMarginTop', $value);
    }

    /**
     * Get cell margin left.
     *
     * @return int
     */
    public function getCellMarginLeft()
    {
        return $this->getTableOnlyProperty('cellMarginLeft');
    }

    /**
     * Set cell margin left.
     *
     * @param int $value
     *
     * @return self
     */
    public function setCellMarginLeft($value = null)
    {
        return $this->setTableOnlyProperty('cellMarginLeft', $value);
    }

    /**
     * Get cell margin right.
     *
     * @return int
     */
    public function getCellMarginRight()
    {
        return $this->getTableOnlyProperty('cellMarginRight');
    }

    /**
     * Set cell margin right.
     *
     * @param int $value
     *
     * @return self
     */
    public function setCellMarginRight($value = null)
    {
        return $this->setTableOnlyProperty('cellMarginRight', $value);
    }

    /**
     * Get cell margin bottom.
     *
     * @return int
     */
    public function getCellMarginBottom()
    {
        return $this->getTableOnlyProperty('cellMarginBottom');
    }

    /**
     * Set cell margin bottom.
     *
     * @param int $value
     *
     * @return self
     */
    public function setCellMarginBottom($value = null)
    {
        return $this->setTableOnlyProperty('cellMarginBottom', $value);
    }

    /**
     * Get cell margin.
     *
     * @return int[]
     */
    public function getCellMargin()
    {
        return [
            $this->cellMarginTop,
            $this->cellMarginLeft,
            $this->cellMarginRight,
            $this->cellMarginBottom,
        ];
    }

    /**
     * Set TLRB cell margin.
     *
     * @param int $value Margin in twips
     *
     * @return self
     */
    public function setCellMargin($value = null)
    {
        $this->setCellMarginTop($value);
        $this->setCellMarginLeft($value);
        $this->setCellMarginRight($value);
        $this->setCellMarginBottom($value);

        return $this;
    }

    /**
     * Check if any of the margin is not null.
     *
     * @return bool
     */
    public function hasMargin()
    {
        $margins = $this->getCellMargin();

        return $margins !== array_filter($margins, 'is_null');
    }

    /**
     * Get shading.
     *
     * @return Shading
     */
    public function getShading()
    {
        return $this->shading;
    }

    /**
     * Set shading.
     *
     * @param mixed $value
     *
     * @return self
     */
    public function setShading($value = null)
    {
        $this->setObjectVal($value, 'Shading', $this->shading);

        return $this;
    }

    /**
     * @since 0.13.0
     *
     * @return string
     */
    public function getAlignment()
    {
        return $this->alignment;
    }

    /**
     * @since 0.13.0
     *
     * @param string $value
     *
     * @return self
     */
    public function setAlignment($value)
    {
        if (JcTable::isValid($value) || Jc::isValid($value)) {
            $this->alignment = $value;
        }

        return $this;
    }

    /**
     * Get width.
     *
     * @return float|int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set width.
     *
     * @param float|int $value
     *
     * @return self
     */
    public function setWidth($value = null)
    {
        $this->width = $this->setNumericVal($value, $this->width);

        return $this;
    }

    /**
     * Get width unit.
     *
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * Set width unit.
     *
     * @param string $value
     *
     * @return self
     */
    public function setUnit($value = null)
    {
        TblWidth::validate($value);
        $this->unit = $value;

        return $this;
    }

    /**
     * Get layout.
     *
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * Set layout.
     *
     * @param string $value
     *
     * @return self
     */
    public function setLayout($value = null)
    {
        $enum = [self::LAYOUT_AUTO, self::LAYOUT_FIXED];
        $this->layout = $this->setEnumVal($value, $enum, $this->layout);

        return $this;
    }

    /**
     * Get table style only property by checking if it's a firstRow.
     *
     * This is necessary since firstRow style is cloned from table style but
     * without certain properties activated, e.g. margins
     *
     * @param string $property
     *
     * @return null|int|string
     */
    private function getTableOnlyProperty($property)
    {
        if (false === $this->isFirstRow) {
            return $this->$property;
        }

        return null;
    }

    /**
     * Set table style only property by checking if it's a firstRow.
     *
     * This is necessary since firstRow style is cloned from table style but
     * without certain properties activated, e.g. margins
     *
     * @param string $property
     * @param int|string $value
     * @param bool $isNumeric
     *
     * @return self
     */
    private function setTableOnlyProperty($property, $value, $isNumeric = true)
    {
        if (false === $this->isFirstRow) {
            if (true === $isNumeric) {
                $this->$property = $this->setNumericVal($value, $this->$property);
            } else {
                $this->$property = $value;
            }
        }

        return $this;
    }

    /**
     * Get position.
     *
     * @return ?TablePosition
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set position.
     *
     * @param mixed $value
     *
     * @return self
     */
    public function setPosition($value = null)
    {
        $this->setObjectVal($value, 'TablePosition', $this->position);

        return $this;
    }

    /**
     * @return ?TblWidthComplexType
     */
    public function getIndent()
    {
        return $this->indent;
    }

    /**
     * @return self
     *
     * @see http://www.datypic.com/sc/ooxml/e-w_tblInd-1.html
     */
    public function setIndent(TblWidthComplexType $indent)
    {
        $this->indent = $indent;

        return $this;
    }

    /**
     * Get the columnWidths.
     *
     * @return null|int[]
     */
    public function getColumnWidths()
    {
        return $this->columnWidths;
    }

    /**
     * The column widths.
     *
     * @param int[] $value
     */
    public function setColumnWidths(?array $value = null): void
    {
        $this->columnWidths = $value;
    }

    /**
     * Get bidiVisual.
     *
     * @return ?bool
     */
    public function isBidiVisual()
    {
        return $this->bidiVisual ?? Settings::isDefaultRtl();
    }

    /**
     * Set bidiVisual.
     *
     * @param ?bool $bidi
     *            Set to true to visually present table as Right to Left
     *
     * @return self
     */
    public function setBidiVisual($bidi)
    {
        $this->bidiVisual = $bidi;

        return $this;
    }
}
