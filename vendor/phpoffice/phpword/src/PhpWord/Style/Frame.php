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

use PhpOffice\PhpWord\SimpleType\Jc;

/**
 * Frame defines the size and position of an object.
 *
 * Width, height, left/hpos, top/vpos, hrel, vrel, wrap, zindex
 *
 * @since 0.12.0
 *
 * @todo Make existing style (image, textbox, etc) use this style
 */
class Frame extends AbstractStyle
{
    /**
     * Length unit.
     *
     * @const string
     */
    const UNIT_PT = 'pt'; // Mostly for shapes
    const UNIT_PX = 'px'; // Mostly for images

    /**
     * General positioning options.
     *
     * @const string
     */
    const POS_ABSOLUTE = 'absolute';
    const POS_RELATIVE = 'relative';

    /**
     * Horizontal/vertical value.
     *
     * @const string
     */
    const POS_CENTER = 'center';
    const POS_LEFT = 'left';
    const POS_RIGHT = 'right';
    const POS_TOP = 'top';
    const POS_BOTTOM = 'bottom';
    const POS_INSIDE = 'inside';
    const POS_OUTSIDE = 'outside';

    /**
     * Position relative to.
     *
     * @const string
     */
    const POS_RELTO_MARGIN = 'margin';
    const POS_RELTO_PAGE = 'page';
    const POS_RELTO_COLUMN = 'column'; // horizontal only
    const POS_RELTO_CHAR = 'char'; // horizontal only
    const POS_RELTO_TEXT = 'text'; // vertical only
    const POS_RELTO_LINE = 'line'; // vertical only
    const POS_RELTO_LMARGIN = 'left-margin-area'; // horizontal only
    const POS_RELTO_RMARGIN = 'right-margin-area'; // horizontal only
    const POS_RELTO_TMARGIN = 'top-margin-area'; // vertical only
    const POS_RELTO_BMARGIN = 'bottom-margin-area'; // vertical only
    const POS_RELTO_IMARGIN = 'inner-margin-area';
    const POS_RELTO_OMARGIN = 'outer-margin-area';

    /**
     * Wrap type.
     *
     * @const string
     */
    const WRAP_INLINE = 'inline';
    const WRAP_SQUARE = 'square';
    const WRAP_TIGHT = 'tight';
    const WRAP_THROUGH = 'through';
    const WRAP_TOPBOTTOM = 'topAndBottom';
    const WRAP_BEHIND = 'behind';
    const WRAP_INFRONT = 'infront';

    /**
     * @var string
     */
    private $alignment = '';

    /**
     * Unit.
     *
     * @var string
     */
    private $unit = 'pt';

    /**
     * Width.
     *
     * @var float|int
     */
    private $width;

    /**
     * Height.
     *
     * @var float|int
     */
    private $height;

    /**
     * Leftmost (horizontal) position.
     *
     * @var float|int
     */
    private $left = 0;

    /**
     * Topmost (vertical) position.
     *
     * @var float|int
     */
    private $top = 0;

    /**
     * Position type: absolute|relative.
     *
     * @var string
     */
    private $pos;

    /**
     * Horizontal position.
     *
     * @var string
     */
    private $hPos;

    /**
     * Horizontal position relative to.
     *
     * @var string
     */
    private $hPosRelTo;

    /**
     * Vertical position.
     *
     * @var string
     */
    private $vPos;

    /**
     * Vertical position relative to.
     *
     * @var string
     */
    private $vPosRelTo;

    /**
     * Wrap type.
     *
     * @var string
     */
    private $wrap;

    /**
     * Top wrap distance.
     *
     * @var float
     */
    private $wrapDistanceTop;

    /**
     * Bottom wrap distance.
     *
     * @var float
     */
    private $wrapDistanceBottom;

    /**
     * Left wrap distance.
     *
     * @var float
     */
    private $wrapDistanceLeft;

    /**
     * Right wrap distance.
     *
     * @var float
     */
    private $wrapDistanceRight;

    /**
     * Vertically raised or lowered text.
     *
     * @var int
     *
     * @see http://www.datypic.com/sc/ooxml/e-w_position-1.html
     */
    private $position;

    /**
     * Create a new instance.
     *
     * @param array $style
     */
    public function __construct($style = [])
    {
        $this->setStyleByArray($style);
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
        if (Jc::isValid($value)) {
            $this->alignment = $value;
        }

        return $this;
    }

    /**
     * Get unit.
     *
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * Set unit.
     *
     * @param string $value
     *
     * @return self
     */
    public function setUnit($value)
    {
        $this->unit = $value;

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
        $this->width = $this->setNumericVal($value, null);

        return $this;
    }

    /**
     * Get height.
     *
     * @return float|int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set height.
     *
     * @param float|int $value
     *
     * @return self
     */
    public function setHeight($value = null)
    {
        $this->height = $this->setNumericVal($value, null);

        return $this;
    }

    /**
     * Get left.
     *
     * @return float|int
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * Set left.
     *
     * @param float|int $value
     *
     * @return self
     */
    public function setLeft($value = 0)
    {
        $this->left = $this->setNumericVal($value, 0);

        return $this;
    }

    /**
     * Get topmost position.
     *
     * @return float|int
     */
    public function getTop()
    {
        return $this->top;
    }

    /**
     * Set topmost position.
     *
     * @param float|int $value
     *
     * @return self
     */
    public function setTop($value = 0)
    {
        $this->top = $this->setNumericVal($value, 0);

        return $this;
    }

    /**
     * Get position type.
     *
     * @return string
     */
    public function getPos()
    {
        return $this->pos;
    }

    /**
     * Set position type.
     *
     * @param string $value
     *
     * @return self
     */
    public function setPos($value)
    {
        $enum = [
            self::POS_ABSOLUTE,
            self::POS_RELATIVE,
        ];
        $this->pos = $this->setEnumVal($value, $enum, $this->pos);

        return $this;
    }

    /**
     * Get horizontal position.
     *
     * @return string
     */
    public function getHPos()
    {
        return $this->hPos;
    }

    /**
     * Set horizontal position.
     *
     * @since 0.12.0 "absolute" option is available.
     *
     * @param string $value
     *
     * @return self
     */
    public function setHPos($value)
    {
        $enum = [
            self::POS_ABSOLUTE,
            self::POS_LEFT,
            self::POS_CENTER,
            self::POS_RIGHT,
            self::POS_INSIDE,
            self::POS_OUTSIDE,
        ];
        $this->hPos = $this->setEnumVal($value, $enum, $this->hPos);

        return $this;
    }

    /**
     * Get vertical position.
     *
     * @return string
     */
    public function getVPos()
    {
        return $this->vPos;
    }

    /**
     * Set vertical position.
     *
     * @since 0.12.0 "absolute" option is available.
     *
     * @param string $value
     *
     * @return self
     */
    public function setVPos($value)
    {
        $enum = [
            self::POS_ABSOLUTE,
            self::POS_TOP,
            self::POS_CENTER,
            self::POS_BOTTOM,
            self::POS_INSIDE,
            self::POS_OUTSIDE,
        ];
        $this->vPos = $this->setEnumVal($value, $enum, $this->vPos);

        return $this;
    }

    /**
     * Get horizontal position relative to.
     *
     * @return string
     */
    public function getHPosRelTo()
    {
        return $this->hPosRelTo;
    }

    /**
     * Set horizontal position relative to.
     *
     * @param string $value
     *
     * @return self
     */
    public function setHPosRelTo($value)
    {
        $enum = [
            self::POS_RELTO_MARGIN,
            self::POS_RELTO_PAGE,
            self::POS_RELTO_COLUMN,
            self::POS_RELTO_CHAR,
            self::POS_RELTO_LMARGIN,
            self::POS_RELTO_RMARGIN,
            self::POS_RELTO_IMARGIN,
            self::POS_RELTO_OMARGIN,
        ];
        $this->hPosRelTo = $this->setEnumVal($value, $enum, $this->hPosRelTo);

        return $this;
    }

    /**
     * Get vertical position relative to.
     *
     * @return string
     */
    public function getVPosRelTo()
    {
        return $this->vPosRelTo;
    }

    /**
     * Set vertical position relative to.
     *
     * @param string $value
     *
     * @return self
     */
    public function setVPosRelTo($value)
    {
        $enum = [
            self::POS_RELTO_MARGIN,
            self::POS_RELTO_PAGE,
            self::POS_RELTO_TEXT,
            self::POS_RELTO_LINE,
            self::POS_RELTO_TMARGIN,
            self::POS_RELTO_BMARGIN,
            self::POS_RELTO_IMARGIN,
            self::POS_RELTO_OMARGIN,
        ];
        $this->vPosRelTo = $this->setEnumVal($value, $enum, $this->vPosRelTo);

        return $this;
    }

    /**
     * Get wrap type.
     *
     * @return string
     */
    public function getWrap()
    {
        return $this->wrap;
    }

    /**
     * Set wrap type.
     *
     * @param string $value
     *
     * @return self
     */
    public function setWrap($value)
    {
        $enum = [
            self::WRAP_INLINE,
            self::WRAP_SQUARE,
            self::WRAP_TIGHT,
            self::WRAP_THROUGH,
            self::WRAP_TOPBOTTOM,
            self::WRAP_BEHIND,
            self::WRAP_INFRONT,
        ];
        $this->wrap = $this->setEnumVal($value, $enum, $this->wrap);

        return $this;
    }

    /**
     * Get top distance from text wrap.
     *
     * @return float
     */
    public function getWrapDistanceTop()
    {
        return $this->wrapDistanceTop;
    }

    /**
     * Set top distance from text wrap.
     *
     * @param int $value
     *
     * @return self
     */
    public function setWrapDistanceTop($value = null)
    {
        $this->wrapDistanceTop = $this->setFloatVal($value, null);

        return $this;
    }

    /**
     * Get bottom distance from text wrap.
     *
     * @return float
     */
    public function getWrapDistanceBottom()
    {
        return $this->wrapDistanceBottom;
    }

    /**
     * Set bottom distance from text wrap.
     *
     * @param float $value
     *
     * @return self
     */
    public function setWrapDistanceBottom($value = null)
    {
        $this->wrapDistanceBottom = $this->setFloatVal($value, null);

        return $this;
    }

    /**
     * Get left distance from text wrap.
     *
     * @return float
     */
    public function getWrapDistanceLeft()
    {
        return $this->wrapDistanceLeft;
    }

    /**
     * Set left distance from text wrap.
     *
     * @param float $value
     *
     * @return self
     */
    public function setWrapDistanceLeft($value = null)
    {
        $this->wrapDistanceLeft = $this->setFloatVal($value, null);

        return $this;
    }

    /**
     * Get right distance from text wrap.
     *
     * @return float
     */
    public function getWrapDistanceRight()
    {
        return $this->wrapDistanceRight;
    }

    /**
     * Set right distance from text wrap.
     *
     * @param float $value
     *
     * @return self
     */
    public function setWrapDistanceRight($value = null)
    {
        $this->wrapDistanceRight = $this->setFloatVal($value, null);

        return $this;
    }

    /**
     * Get position.
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set position.
     *
     * @param int $value
     *
     * @return self
     */
    public function setPosition($value = null)
    {
        $this->position = $this->setIntVal($value, null);

        return $this;
    }
}
