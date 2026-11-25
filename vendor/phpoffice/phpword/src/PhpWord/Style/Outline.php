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
 * Outline defines the line/border of the object.
 *
 * @see  http://www.schemacentral.com/sc/ooxml/t-v_CT_Stroke.html
 * @see  http://www.w3.org/TR/1998/NOTE-VML-19980513#_Toc416858395
 * @since 0.12.0
 */
class Outline extends AbstractStyle
{
    /**
     * Line style constants.
     *
     * @see  http://www.schemacentral.com/sc/ooxml/t-v_ST_StrokeLineStyle.html
     *
     * @const string
     */
    const LINE_SINGLE = 'single';
    const LINE_THIN_THIN = 'thinThin';
    const LINE_THIN_THICK = 'thinThick';
    const LINE_THICK_THIN = 'thickThin';
    const LINE_THICK_BETWEEN_THIN = 'thickBetweenThin';

    /**
     * Line style constants.
     *
     * @see  http://www.schemacentral.com/sc/ooxml/t-v_ST_StrokeEndCap.html
     *
     * @const string
     */
    const ENDCAP_FLAT = 'flat';
    const ENDCAP_SQUARE = 'square';
    const ENDCAP_ROUND = 'round';

    /**
     * Arrowhead type constants.
     *
     * @see  http://www.schemacentral.com/sc/ooxml/t-v_ST_StrokeArrowType.html
     *
     * @const string
     */
    const ARROW_NONE = 'none';
    const ARROW_BLOCK = 'block';
    const ARROW_CLASSIC = 'classic';
    const ARROW_OVAL = 'oval';
    const ARROW_DIAMOND = 'diamond';
    const ARROW_OPEN = 'open';

    /**
     * Unit; No set method for now.
     *
     * @var string
     */
    private $unit = 'pt';

    /**
     * Outline weight.
     *
     * @var float|int
     */
    private $weight;

    /**
     * Outline color.
     *
     * @var string
     */
    private $color;

    /**
     * Dash type.
     *
     * @var string
     */
    private $dash;

    /**
     * Line style.
     *
     * @var string
     */
    private $line;

    /**
     * End cap.
     *
     * @var string
     *
     * @see  http://www.schemacentral.com/sc/ooxml/t-v_ST_StrokeEndCap.html
     */
    private $endCap;

    /**
     * Start arrow type.
     *
     * @var string
     */
    private $startArrow;

    /**
     * End arrow type.
     *
     * @var string
     */
    private $endArrow;

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
     * Get unit.
     *
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * Get weight.
     *
     * @return float|int
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set weight.
     *
     * @param float|int $value
     *
     * @return self
     */
    public function setWeight($value = null)
    {
        $this->weight = $this->setNumericVal($value, null);

        return $this;
    }

    /**
     * Get color.
     *
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set color.
     *
     * @param string $value
     *
     * @return self
     */
    public function setColor($value = null)
    {
        $this->color = $value;

        return $this;
    }

    /**
     * Get dash type.
     *
     * @return string
     */
    public function getDash()
    {
        return $this->dash;
    }

    /**
     * Set dash type.
     *
     * @param string $value
     *
     * @return self
     */
    public function setDash($value = null)
    {
        $this->dash = $value;

        return $this;
    }

    /**
     * Get line style.
     *
     * @return string
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * Set line style.
     *
     * @param string $value
     *
     * @return self
     */
    public function setLine($value = null)
    {
        $enum = [self::LINE_SINGLE, self::LINE_THIN_THIN, self::LINE_THIN_THICK,
            self::LINE_THICK_THIN, self::LINE_THICK_BETWEEN_THIN, ];
        $this->line = $this->setEnumVal($value, $enum, null);

        return $this;
    }

    /**
     * Get endCap style.
     *
     * @return string
     */
    public function getEndCap()
    {
        return $this->endCap;
    }

    /**
     * Set endCap style.
     *
     * @param string $value
     *
     * @return self
     */
    public function setEndCap($value = null)
    {
        $enum = [self::ENDCAP_FLAT, self::ENDCAP_SQUARE, self::ENDCAP_ROUND];
        $this->endCap = $this->setEnumVal($value, $enum, null);

        return $this;
    }

    /**
     * Get startArrow.
     *
     * @return string
     */
    public function getStartArrow()
    {
        return $this->startArrow;
    }

    /**
     * Set pattern.
     *
     * @param string $value
     *
     * @return self
     */
    public function setStartArrow($value = null)
    {
        $enum = [self::ARROW_NONE, self::ARROW_BLOCK, self::ARROW_CLASSIC,
            self::ARROW_OVAL, self::ARROW_DIAMOND, self::ARROW_OPEN, ];
        $this->startArrow = $this->setEnumVal($value, $enum, null);

        return $this;
    }

    /**
     * Get endArrow.
     *
     * @return string
     */
    public function getEndArrow()
    {
        return $this->endArrow;
    }

    /**
     * Set pattern.
     *
     * @param string $value
     *
     * @return self
     */
    public function setEndArrow($value = null)
    {
        $enum = [self::ARROW_NONE, self::ARROW_BLOCK, self::ARROW_CLASSIC,
            self::ARROW_OVAL, self::ARROW_DIAMOND, self::ARROW_OPEN, ];
        $this->endArrow = $this->setEnumVal($value, $enum, null);

        return $this;
    }
}
