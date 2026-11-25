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

/**
 * Border style.
 */
class Border extends AbstractStyle
{
    const DEFAULT_MARGIN = 1440;           // In twips.

    /**
     * Border Top Size.
     *
     * @var float|int
     */
    protected $borderTopSize;

    /**
     * Border Top Color.
     *
     * @var null|string
     */
    protected $borderTopColor;

    /**
     * Border Top Style.
     *
     * @var string
     */
    protected $borderTopStyle;

    /**
     * Border Left Size.
     *
     * @var float|int
     */
    protected $borderLeftSize;

    /**
     * Border Left Color.
     *
     * @var null|string
     */
    protected $borderLeftColor;

    /**
     * Border Left Style.
     *
     * @var string
     */
    protected $borderLeftStyle;

    /**
     * Border Right Size.
     *
     * @var float|int
     */
    protected $borderRightSize;

    /**
     * Border Right Color.
     *
     * @var null|string
     */
    protected $borderRightColor;

    /**
     * Border Right Style.
     *
     * @var string
     */
    protected $borderRightStyle;

    /**
     * Border Bottom Size.
     *
     * @var float|int
     */
    protected $borderBottomSize;

    /**
     * Border Bottom Color.
     *
     * @var null|string
     */
    protected $borderBottomColor;

    /**
     * Border Bottom Style.
     *
     * @var string
     */
    protected $borderBottomStyle;

    /**
     * Top margin spacing.
     *
     * @var float|int
     */
    protected $marginTop = self::DEFAULT_MARGIN;

    /**
     * Left margin spacing.
     *
     * @var float|int
     */
    protected $marginLeft = self::DEFAULT_MARGIN;

    /**
     * Right margin spacing.
     *
     * @var float|int
     */
    protected $marginRight = self::DEFAULT_MARGIN;

    /**
     * Bottom margin spacing.
     *
     * @var float|int
     */
    protected $marginBottom = self::DEFAULT_MARGIN;

    /**
     * Get border size.
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
        ];
    }

    /**
     * Set border size.
     *
     * @param float|int $value
     *
     * @return self
     */
    public function setBorderSize($value = null)
    {
        $this->setBorderTopSize($value);
        $this->setBorderLeftSize($value);
        $this->setBorderRightSize($value);
        $this->setBorderBottomSize($value);

        return $this;
    }

    /**
     * Get border color.
     *
     * @return array<null|string>
     */
    public function getBorderColor()
    {
        return [
            $this->getBorderTopColor(),
            $this->getBorderLeftColor(),
            $this->getBorderRightColor(),
            $this->getBorderBottomColor(),
        ];
    }

    /**
     * Set border color.
     *
     * @param null|string $value
     *
     * @return self
     */
    public function setBorderColor($value = null)
    {
        $this->setBorderTopColor($value);
        $this->setBorderLeftColor($value);
        $this->setBorderRightColor($value);
        $this->setBorderBottomColor($value);

        return $this;
    }

    /**
     * Get border style.
     *
     * @return string[]
     */
    public function getBorderStyle()
    {
        return [
            $this->getBorderTopStyle(),
            $this->getBorderLeftStyle(),
            $this->getBorderRightStyle(),
            $this->getBorderBottomStyle(),
        ];
    }

    /**
     * Set border style.
     *
     * @param string $value
     *
     * @return self
     */
    public function setBorderStyle($value = null)
    {
        $this->setBorderTopStyle($value);
        $this->setBorderLeftStyle($value);
        $this->setBorderRightStyle($value);
        $this->setBorderBottomStyle($value);

        return $this;
    }

    /**
     * Get border top size.
     *
     * @return float|int
     */
    public function getBorderTopSize()
    {
        return $this->borderTopSize;
    }

    /**
     * Set border top size.
     *
     * @param float|int $value
     *
     * @return self
     */
    public function setBorderTopSize($value = null)
    {
        $this->borderTopSize = $this->setNumericVal($value, $this->borderTopSize);

        return $this;
    }

    /**
     * Get border top color.
     *
     * @return null|string
     */
    public function getBorderTopColor()
    {
        return $this->borderTopColor;
    }

    /**
     * Set border top color.
     *
     * @param null|string $value
     *
     * @return self
     */
    public function setBorderTopColor($value = null)
    {
        $this->borderTopColor = $value;

        return $this;
    }

    /**
     * Get border top style.
     *
     * @return string
     */
    public function getBorderTopStyle()
    {
        return $this->borderTopStyle;
    }

    /**
     * Set border top Style.
     *
     * @param string $value
     *
     * @return self
     */
    public function setBorderTopStyle($value = null)
    {
        $this->borderTopStyle = $value;

        return $this;
    }

    /**
     * Get border left size.
     *
     * @return float|int
     */
    public function getBorderLeftSize()
    {
        return $this->borderLeftSize;
    }

    /**
     * Set border left size.
     *
     * @param float|int $value
     *
     * @return self
     */
    public function setBorderLeftSize($value = null)
    {
        $this->borderLeftSize = $this->setNumericVal($value, $this->borderLeftSize);

        return $this;
    }

    /**
     * Get border left color.
     *
     * @return null|string
     */
    public function getBorderLeftColor()
    {
        return $this->borderLeftColor;
    }

    /**
     * Set border left color.
     *
     * @param null|string $value
     *
     * @return self
     */
    public function setBorderLeftColor($value = null)
    {
        $this->borderLeftColor = $value;

        return $this;
    }

    /**
     * Get border left style.
     *
     * @return string
     */
    public function getBorderLeftStyle()
    {
        return $this->borderLeftStyle;
    }

    /**
     * Set border left style.
     *
     * @param string $value
     *
     * @return self
     */
    public function setBorderLeftStyle($value = null)
    {
        $this->borderLeftStyle = $value;

        return $this;
    }

    /**
     * Get border right size.
     *
     * @return float|int
     */
    public function getBorderRightSize()
    {
        return $this->borderRightSize;
    }

    /**
     * Set border right size.
     *
     * @param float|int $value
     *
     * @return self
     */
    public function setBorderRightSize($value = null)
    {
        $this->borderRightSize = $this->setNumericVal($value, $this->borderRightSize);

        return $this;
    }

    /**
     * Get border right color.
     *
     * @return null|string
     */
    public function getBorderRightColor()
    {
        return $this->borderRightColor;
    }

    /**
     * Set border right color.
     *
     * @param null|string $value
     *
     * @return self
     */
    public function setBorderRightColor($value = null)
    {
        $this->borderRightColor = $value;

        return $this;
    }

    /**
     * Get border right style.
     *
     * @return string
     */
    public function getBorderRightStyle()
    {
        return $this->borderRightStyle;
    }

    /**
     * Set border right style.
     *
     * @param string $value
     *
     * @return self
     */
    public function setBorderRightStyle($value = null)
    {
        $this->borderRightStyle = $value;

        return $this;
    }

    /**
     * Get border bottom size.
     *
     * @return float|int
     */
    public function getBorderBottomSize()
    {
        return $this->borderBottomSize;
    }

    /**
     * Set border bottom size.
     *
     * @param float|int $value
     *
     * @return self
     */
    public function setBorderBottomSize($value = null)
    {
        $this->borderBottomSize = $this->setNumericVal($value, $this->borderBottomSize);

        return $this;
    }

    /**
     * Get border bottom color.
     *
     * @return null|string
     */
    public function getBorderBottomColor()
    {
        return $this->borderBottomColor;
    }

    /**
     * Set border bottom color.
     *
     * @param null|string $value
     *
     * @return self
     */
    public function setBorderBottomColor($value = null)
    {
        $this->borderBottomColor = $value;

        return $this;
    }

    /**
     * Get border bottom style.
     *
     * @return string
     */
    public function getBorderBottomStyle()
    {
        return $this->borderBottomStyle;
    }

    /**
     * Set border bottom style.
     *
     * @param string $value
     *
     * @return self
     */
    public function setBorderBottomStyle($value = null)
    {
        $this->borderBottomStyle = $value;

        return $this;
    }

    /**
     * Check if any of the border is not null.
     *
     * @return bool
     */
    public function hasBorder()
    {
        $borders = $this->getBorderSize();

        return $borders !== array_filter($borders, 'is_null');
    }

    /**
     * Get Margin Top.
     *
     * @return float|int
     */
    public function getMarginTop()
    {
        return $this->marginTop;
    }

    /**
     * Set Margin Top.
     *
     * @param float|int $value
     *
     * @return self
     */
    public function setMarginTop($value = null)
    {
        $this->marginTop = $this->setNumericVal($value, self::DEFAULT_MARGIN);

        return $this;
    }

    /**
     * Get Margin Left.
     *
     * @return float|int
     */
    public function getMarginLeft()
    {
        return $this->marginLeft;
    }

    /**
     * Set Margin Left.
     *
     * @param float|int $value
     *
     * @return self
     */
    public function setMarginLeft($value = null)
    {
        $this->marginLeft = $this->setNumericVal($value, self::DEFAULT_MARGIN);

        return $this;
    }

    /**
     * Get Margin Right.
     *
     * @return float|int
     */
    public function getMarginRight()
    {
        return $this->marginRight;
    }

    /**
     * Set Margin Right.
     *
     * @param float|int $value
     *
     * @return self
     */
    public function setMarginRight($value = null)
    {
        $this->marginRight = $this->setNumericVal($value, self::DEFAULT_MARGIN);

        return $this;
    }

    /**
     * Get Margin Bottom.
     *
     * @return float|int
     */
    public function getMarginBottom()
    {
        return $this->marginBottom;
    }

    /**
     * Set Margin Bottom.
     *
     * @param float|int $value
     *
     * @return self
     */
    public function setMarginBottom($value = null)
    {
        $this->marginBottom = $this->setNumericVal($value, self::DEFAULT_MARGIN);

        return $this;
    }
}
