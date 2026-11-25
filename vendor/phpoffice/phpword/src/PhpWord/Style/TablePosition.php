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
 * TablePosition style.
 *
 * @see http://www.datypic.com/sc/ooxml/e-w_tblpPr-1.html
 */
class TablePosition extends AbstractStyle
{
    /**
     * Vertical anchor constants.
     *
     * @const string
     *
     * @see http://www.datypic.com/sc/ooxml/t-w_ST_VAnchor.html
     */
    const VANCHOR_TEXT = 'text'; // Relative to vertical text extents
    const VANCHOR_MARGIN = 'margin'; // Relative to margin
    const VANCHOR_PAGE = 'page'; // Relative to page

    /**
     * Horizontal anchor constants.
     *
     * @const string
     *
     * @see http://www.datypic.com/sc/ooxml/t-w_ST_HAnchor.html
     */
    const HANCHOR_TEXT = 'text'; // Relative to text extents
    const HANCHOR_MARGIN = 'margin'; // Relative to margin
    const HANCHOR_PAGE = 'page'; // Relative to page

    /**
     * Horizontal alignment constants.
     *
     * @const string
     *
     * @see http://www.datypic.com/sc/ooxml/t-w_ST_XAlign.html
     */
    const XALIGN_LEFT = 'left'; // Left aligned horizontally
    const XALIGN_CENTER = 'center'; // Centered horizontally
    const XALIGN_RIGHT = 'right'; // Right aligned horizontally
    const XALIGN_INSIDE = 'inside'; // Inside
    const XALIGN_OUTSIDE = 'outside'; // Outside

    /**
     * Vertical alignment constants.
     *
     * @const string
     *
     * @see http://www.datypic.com/sc/ooxml/t-w_ST_YAlign.html
     */
    const YALIGN_INLINE = 'inline'; // In line with text
    const YALIGN_TOP = 'top'; // Top
    const YALIGN_CENTER = 'center'; // Centered vertically
    const YALIGN_BOTTOM = 'bottom'; // Bottom
    const YALIGN_INSIDE = 'inside'; // Inside Anchor Extents
    const YALIGN_OUTSIDE = 'outside'; // Centered vertically

    /**
     * Distance from left of table to text.
     *
     * @var int
     */
    private $leftFromText;

    /**
     * Distance from right of table to text.
     *
     * @var int
     */
    private $rightFromText;

    /**
     * Distance from top of table to text.
     *
     * @var int
     */
    private $topFromText;

    /**
     * Distance from bottom of table to text.
     *
     * @var int
     */
    private $bottomFromText;

    /**
     * Table vertical anchor.
     *
     * @var string
     *
     * @see http://www.datypic.com/sc/ooxml/t-w_ST_VAnchor.html
     */
    private $vertAnchor;

    /**
     * Table horizontal anchor.
     *
     * @var string
     *
     * @see http://www.datypic.com/sc/ooxml/t-w_ST_HAnchor.html
     */
    private $horzAnchor;

    /**
     * Relative horizontal alignment from anchor.
     *
     * @var string
     *
     * @see http://www.datypic.com/sc/ooxml/t-w_ST_XAlign.html
     */
    private $tblpXSpec;

    /**
     * Absolute horizontal distance from anchor.
     *
     * @var int
     */
    private $tblpX;

    /**
     * Relative vertical alignment from anchor.
     *
     * @var string
     *
     * @see http://www.datypic.com/sc/ooxml/t-w_ST_YAlign.html
     */
    private $tblpYSpec;

    /**
     * Absolute vertical distance from anchor.
     *
     * @var int
     */
    private $tblpY;

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
     * Get distance from left of table to text.
     *
     * @return int
     */
    public function getLeftFromText()
    {
        return $this->leftFromText;
    }

    /**
     * Set distance from left of table to text.
     *
     * @param int $value
     *
     * @return self
     */
    public function setLeftFromText($value = null)
    {
        $this->leftFromText = $this->setNumericVal($value, $this->leftFromText);

        return $this;
    }

    /**
     * Get distance from right of table to text.
     *
     * @return int
     */
    public function getRightFromText()
    {
        return $this->rightFromText;
    }

    /**
     * Set distance from right of table to text.
     *
     * @param int $value
     *
     * @return self
     */
    public function setRightFromText($value = null)
    {
        $this->rightFromText = $this->setNumericVal($value, $this->rightFromText);

        return $this;
    }

    /**
     * Get distance from top of table to text.
     *
     * @return int
     */
    public function getTopFromText()
    {
        return $this->topFromText;
    }

    /**
     * Set distance from top of table to text.
     *
     * @param int $value
     *
     * @return self
     */
    public function setTopFromText($value = null)
    {
        $this->topFromText = $this->setNumericVal($value, $this->topFromText);

        return $this;
    }

    /**
     * Get distance from bottom of table to text.
     *
     * @return int
     */
    public function getBottomFromText()
    {
        return $this->bottomFromText;
    }

    /**
     * Set distance from bottom of table to text.
     *
     * @param int $value
     *
     * @return self
     */
    public function setBottomFromText($value = null)
    {
        $this->bottomFromText = $this->setNumericVal($value, $this->bottomFromText);

        return $this;
    }

    /**
     * Get table vertical anchor.
     *
     * @return string
     */
    public function getVertAnchor()
    {
        return $this->vertAnchor;
    }

    /**
     * Set table vertical anchor.
     *
     * @param string $value
     *
     * @return self
     */
    public function setVertAnchor($value = null)
    {
        $enum = [
            self::VANCHOR_TEXT,
            self::VANCHOR_MARGIN,
            self::VANCHOR_PAGE,
        ];
        $this->vertAnchor = $this->setEnumVal($value, $enum, $this->vertAnchor);

        return $this;
    }

    /**
     * Get table horizontal anchor.
     *
     * @return string
     */
    public function getHorzAnchor()
    {
        return $this->horzAnchor;
    }

    /**
     * Set table horizontal anchor.
     *
     * @param string $value
     *
     * @return self
     */
    public function setHorzAnchor($value = null)
    {
        $enum = [
            self::HANCHOR_TEXT,
            self::HANCHOR_MARGIN,
            self::HANCHOR_PAGE,
        ];
        $this->horzAnchor = $this->setEnumVal($value, $enum, $this->horzAnchor);

        return $this;
    }

    /**
     * Get relative horizontal alignment from anchor.
     *
     * @return string
     */
    public function getTblpXSpec()
    {
        return $this->tblpXSpec;
    }

    /**
     * Set relative horizontal alignment from anchor.
     *
     * @param string $value
     *
     * @return self
     */
    public function setTblpXSpec($value = null)
    {
        $enum = [
            self::XALIGN_LEFT,
            self::XALIGN_CENTER,
            self::XALIGN_RIGHT,
            self::XALIGN_INSIDE,
            self::XALIGN_OUTSIDE,
        ];
        $this->tblpXSpec = $this->setEnumVal($value, $enum, $this->tblpXSpec);

        return $this;
    }

    /**
     * Get absolute horizontal distance from anchor.
     *
     * @return int
     */
    public function getTblpX()
    {
        return $this->tblpX;
    }

    /**
     * Set absolute horizontal distance from anchor.
     *
     * @param int $value
     *
     * @return self
     */
    public function setTblpX($value = null)
    {
        $this->tblpX = $this->setNumericVal($value, $this->tblpX);

        return $this;
    }

    /**
     * Get relative vertical alignment from anchor.
     *
     * @return string
     */
    public function getTblpYSpec()
    {
        return $this->tblpYSpec;
    }

    /**
     * Set relative vertical alignment from anchor.
     *
     * @param string $value
     *
     * @return self
     */
    public function setTblpYSpec($value = null)
    {
        $enum = [
            self::YALIGN_INLINE,
            self::YALIGN_TOP,
            self::YALIGN_CENTER,
            self::YALIGN_BOTTOM,
            self::YALIGN_INSIDE,
            self::YALIGN_OUTSIDE,
        ];
        $this->tblpYSpec = $this->setEnumVal($value, $enum, $this->tblpYSpec);

        return $this;
    }

    /**
     * Get absolute vertical distance from anchor.
     *
     * @return int
     */
    public function getTblpY()
    {
        return $this->tblpY;
    }

    /**
     * Set absolute vertical distance from anchor.
     *
     * @param int $value
     *
     * @return self
     */
    public function setTblpY($value = null)
    {
        $this->tblpY = $this->setNumericVal($value, $this->tblpY);

        return $this;
    }
}
