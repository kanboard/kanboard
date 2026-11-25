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

use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\SimpleType\VerticalJc;

/**
 * Table cell style.
 */
class Cell extends Border
{
    //Text direction constants
    /**
     * Left to Right, Top to Bottom.
     */
    const TEXT_DIR_LRTB = 'lrTb';
    /**
     * Top to Bottom, Right to Left.
     */
    const TEXT_DIR_TBRL = 'tbRl';
    /**
     * Bottom to Top, Left to Right.
     */
    const TEXT_DIR_BTLR = 'btLr';
    /**
     * Left to Right, Top to Bottom Rotated.
     */
    const TEXT_DIR_LRTBV = 'lrTbV';
    /**
     * Top to Bottom, Right to Left Rotated.
     */
    const TEXT_DIR_TBRLV = 'tbRlV';
    /**
     * Top to Bottom, Left to Right Rotated.
     */
    const TEXT_DIR_TBLRV = 'tbLrV';

    /**
     * Vertical merge (rowspan) constants.
     *
     * @const string
     */
    const VMERGE_RESTART = 'restart';
    const VMERGE_CONTINUE = 'continue';

    /**
     * Default border color.
     *
     * @const string
     */
    const DEFAULT_BORDER_COLOR = '000000';

    /**
     * Vertical align (top, center, both, bottom).
     *
     * @var null|string
     */
    private $vAlign;

    /**
     * @var null|int
     */
    private $paddingTop;

    /**
     * @var null|int
     */
    private $paddingBottom;

    /**
     * @var null|int
     */
    private $paddingLeft;

    /**
     * @var null|int
     */
    private $paddingRight;

    /**
     * Text Direction.
     *
     * @var string
     */
    private $textDirection;

    /**
     * colspan.
     *
     * @var int
     */
    private $gridSpan;

    /**
     * rowspan (restart, continue).
     *
     * - restart: Start/restart merged region
     * - continue: Continue merged region
     *
     * @var null|string
     */
    private $vMerge;

    /**
     * Shading.
     *
     * @var Shading
     */
    private $shading;

    /**
     * Width.
     *
     * @var ?int
     */
    private $width;

    /**
     * Width unit.
     *
     * @var string
     */
    private $unit = TblWidth::TWIP;

    /**
     * Prevent text from wrapping in the cell.
     *
     * @var bool
     */
    private $noWrap = true;

    /**
     * Get vertical align.
     *
     * @return null|string
     */
    public function getVAlign()
    {
        return $this->vAlign;
    }

    /**
     * Set vertical align.
     *
     * @param null|string $value
     *
     * @return self
     */
    public function setVAlign($value = null)
    {
        if ($value === null) {
            $this->vAlign = null;

            return $this;
        }

        VerticalJc::validate($value);
        $this->vAlign = $this->setEnumVal($value, VerticalJc::values(), $this->vAlign);

        return $this;
    }

    /**
     * Get text direction.
     *
     * @return string
     */
    public function getTextDirection()
    {
        return $this->textDirection;
    }

    /**
     * Set text direction.
     *
     * @param string $value
     *
     * @return self
     */
    public function setTextDirection($value = null)
    {
        $enum = [
            self::TEXT_DIR_BTLR,
            self::TEXT_DIR_TBRL,
            self::TEXT_DIR_LRTB,
            self::TEXT_DIR_LRTBV,
            self::TEXT_DIR_TBRLV,
            self::TEXT_DIR_TBLRV,
        ];
        $this->textDirection = $this->setEnumVal($value, $enum, $this->textDirection);

        return $this;
    }

    /**
     * Get background.
     *
     * @return string
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
        return $this->setShading(['fill' => $value]);
    }

    /**
     * Get grid span (colspan).
     *
     * @return int
     */
    public function getGridSpan()
    {
        return $this->gridSpan;
    }

    /**
     * Set grid span (colspan).
     *
     * @param int $value
     *
     * @return self
     */
    public function setGridSpan($value = null)
    {
        $this->gridSpan = $this->setIntVal($value, $this->gridSpan);

        return $this;
    }

    /**
     * Get vertical merge (rowspan).
     *
     * @return null|string
     */
    public function getVMerge()
    {
        return $this->vMerge;
    }

    /**
     * Set vertical merge (rowspan).
     *
     * @param null|string $value
     *
     * @return self
     */
    public function setVMerge($value = null)
    {
        if ($value === null) {
            $this->vMerge = null;

            return $this;
        }

        $enum = [self::VMERGE_RESTART, self::VMERGE_CONTINUE];
        $this->vMerge = $this->setEnumVal($value, $enum, $this->vMerge);

        return $this;
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
     * Get cell width.
     *
     * @return ?int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set cell width.
     *
     * @param int $value
     *
     * @return self
     */
    public function setWidth($value)
    {
        $this->width = $this->setIntVal($value);

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
     */
    public function setUnit($value)
    {
        $this->unit = $this->setEnumVal($value, [TblWidth::AUTO, TblWidth::PERCENT, TblWidth::TWIP], TblWidth::TWIP);

        return $this;
    }

    /**
     * Set noWrap.
     */
    public function setNoWrap(bool $value): self
    {
        $this->noWrap = $this->setBoolVal($value, true);

        return $this;
    }

    /**
     * Get noWrap.
     */
    public function getNoWrap(): bool
    {
        return $this->noWrap;
    }

    /**
     * Get style padding-top.
     */
    public function getPaddingTop(): ?int
    {
        return $this->paddingTop;
    }

    /**
     * Set style padding-top.
     *
     * @return $this
     */
    public function setPaddingTop(int $value): self
    {
        $this->paddingTop = $value;

        return $this;
    }

    /**
     * Get style padding-bottom.
     */
    public function getPaddingBottom(): ?int
    {
        return $this->paddingBottom;
    }

    /**
     * Set style padding-bottom.
     *
     * @return $this
     */
    public function setPaddingBottom(int $value): self
    {
        $this->paddingBottom = $value;

        return $this;
    }

    /**
     * Get style padding-left.
     */
    public function getPaddingLeft(): ?int
    {
        return $this->paddingLeft;
    }

    /**
     * Set style padding-left.
     *
     * @return $this
     */
    public function setPaddingLeft(int $value): self
    {
        $this->paddingLeft = $value;

        return $this;
    }

    /**
     * Get style padding-right.
     */
    public function getPaddingRight(): ?int
    {
        return $this->paddingRight;
    }

    /**
     * Set style padding-right.
     *
     * @return $this
     */
    public function setPaddingRight(int $value): self
    {
        $this->paddingRight = $value;

        return $this;
    }
}
