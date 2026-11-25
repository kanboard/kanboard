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
 * Image and memory image style.
 */
class Image extends Frame
{
    /**
     * Backward compatibility constants.
     *
     * @const string
     */
    const WRAPPING_STYLE_INLINE = self::WRAP_INLINE;
    const WRAPPING_STYLE_SQUARE = self::WRAP_SQUARE;
    const WRAPPING_STYLE_TIGHT = self::WRAP_TIGHT;
    const WRAPPING_STYLE_BEHIND = self::WRAP_BEHIND;
    const WRAPPING_STYLE_INFRONT = self::WRAP_INFRONT;
    const POSITION_HORIZONTAL_LEFT = self::POS_LEFT;
    const POSITION_HORIZONTAL_CENTER = self::POS_CENTER;
    const POSITION_HORIZONTAL_RIGHT = self::POS_RIGHT;
    const POSITION_VERTICAL_TOP = self::POS_TOP;
    const POSITION_VERTICAL_CENTER = self::POS_CENTER;
    const POSITION_VERTICAL_BOTTOM = self::POS_BOTTOM;
    const POSITION_VERTICAL_INSIDE = self::POS_INSIDE;
    const POSITION_VERTICAL_OUTSIDE = self::POS_OUTSIDE;
    const POSITION_RELATIVE_TO_MARGIN = self::POS_RELTO_MARGIN;
    const POSITION_RELATIVE_TO_PAGE = self::POS_RELTO_PAGE;
    const POSITION_RELATIVE_TO_COLUMN = self::POS_RELTO_COLUMN;
    const POSITION_RELATIVE_TO_CHAR = self::POS_RELTO_CHAR;
    const POSITION_RELATIVE_TO_TEXT = self::POS_RELTO_TEXT;
    const POSITION_RELATIVE_TO_LINE = self::POS_RELTO_LINE;
    const POSITION_RELATIVE_TO_LMARGIN = self::POS_RELTO_LMARGIN;
    const POSITION_RELATIVE_TO_RMARGIN = self::POS_RELTO_RMARGIN;
    const POSITION_RELATIVE_TO_TMARGIN = self::POS_RELTO_TMARGIN;
    const POSITION_RELATIVE_TO_BMARGIN = self::POS_RELTO_BMARGIN;
    const POSITION_RELATIVE_TO_IMARGIN = self::POS_RELTO_IMARGIN;
    const POSITION_RELATIVE_TO_OMARGIN = self::POS_RELTO_OMARGIN;
    const POSITION_ABSOLUTE = self::POS_ABSOLUTE;
    const POSITION_RELATIVE = self::POS_RELATIVE;

    /**
     * Create new instance.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setUnit(self::UNIT_PT);

        // Backward compatibility setting
        // @todo Remove on 1.0.0
        $this->setWrap(self::WRAPPING_STYLE_INLINE);
        $this->setHPos(self::POSITION_HORIZONTAL_LEFT);
        $this->setHPosRelTo(self::POSITION_RELATIVE_TO_CHAR);
        $this->setVPos(self::POSITION_VERTICAL_TOP);
        $this->setVPosRelTo(self::POSITION_RELATIVE_TO_LINE);
    }

    /**
     * Get margin top.
     *
     * @return float|int
     */
    public function getMarginTop()
    {
        return $this->getTop();
    }

    /**
     * Set margin top.
     *
     * @ignoreScrutinizerPatch
     *
     * @param float|int $value
     *
     * @return self
     */
    public function setMarginTop($value = 0)
    {
        $this->setTop($value);

        return $this;
    }

    /**
     * Get margin left.
     *
     * @return float|int
     */
    public function getMarginLeft()
    {
        return $this->getLeft();
    }

    /**
     * Set margin left.
     *
     * @ignoreScrutinizerPatch
     *
     * @param float|int $value
     *
     * @return self
     */
    public function setMarginLeft($value = 0)
    {
        $this->setLeft($value);

        return $this;
    }

    /**
     * Get wrapping style.
     *
     * @return string
     */
    public function getWrappingStyle()
    {
        return $this->getWrap();
    }

    /**
     * Set wrapping style.
     *
     * @param string $wrappingStyle
     *
     * @return self
     */
    public function setWrappingStyle($wrappingStyle)
    {
        $this->setWrap($wrappingStyle);

        return $this;
    }

    /**
     * Get positioning type.
     *
     * @return string
     */
    public function getPositioning()
    {
        return $this->getPos();
    }

    /**
     * Set positioning type.
     *
     * @param string $positioning
     *
     * @return self
     */
    public function setPositioning($positioning)
    {
        $this->setPos($positioning);

        return $this;
    }

    /**
     * Get horizontal alignment.
     *
     * @return string
     */
    public function getPosHorizontal()
    {
        return $this->getHPos();
    }

    /**
     * Set horizontal alignment.
     *
     * @param string $alignment
     *
     * @return self
     */
    public function setPosHorizontal($alignment)
    {
        $this->setHPos($alignment);

        return $this;
    }

    /**
     * Get vertical alignment.
     *
     * @return string
     */
    public function getPosVertical()
    {
        return $this->getVPos();
    }

    /**
     * Set vertical alignment.
     *
     * @param string $alignment
     *
     * @return self
     */
    public function setPosVertical($alignment)
    {
        $this->setVPos($alignment);

        return $this;
    }

    /**
     * Get horizontal relation.
     *
     * @return string
     */
    public function getPosHorizontalRel()
    {
        return $this->getHPosRelTo();
    }

    /**
     * Set horizontal relation.
     *
     * @param string $relto
     *
     * @return self
     */
    public function setPosHorizontalRel($relto)
    {
        $this->setHPosRelTo($relto);

        return $this;
    }

    /**
     * Get vertical relation.
     *
     * @return string
     */
    public function getPosVerticalRel()
    {
        return $this->getVPosRelTo();
    }

    /**
     * Set vertical relation.
     *
     * @param string $relto
     *
     * @return self
     */
    public function setPosVerticalRel($relto)
    {
        $this->setVPosRelTo($relto);

        return $this;
    }
}
