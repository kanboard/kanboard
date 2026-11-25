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
 * Line style.
 */
class Line extends Image
{
    /**
     * Connector types.
     *
     * @const string
     */
    const CONNECTOR_TYPE_STRAIGHT = 'straight';

    /**
     * Arrow styles.
     *
     * @const string
     */
    const ARROW_STYLE_BLOCK = 'block';
    const ARROW_STYLE_OPEN = 'open';
    const ARROW_STYLE_CLASSIC = 'classic';
    const ARROW_STYLE_DIAMOND = 'diamond';
    const ARROW_STYLE_OVAL = 'oval';

    /**
     * Dash styles.
     *
     * @const string
     */
    const DASH_STYLE_DASH = 'dash';
    const DASH_STYLE_ROUND_DOT = 'rounddot';
    const DASH_STYLE_SQUARE_DOT = 'squaredot';
    const DASH_STYLE_DASH_DOT = 'dashdot';
    const DASH_STYLE_LONG_DASH = 'longdash';
    const DASH_STYLE_LONG_DASH_DOT = 'longdashdot';
    const DASH_STYLE_LONG_DASH_DOT_DOT = 'longdashdotdot';

    /**
     * flip Line.
     *
     * @var bool
     */
    private $flip = false;

    /**
     * connectorType.
     *
     * @var string
     */
    private $connectorType = self::CONNECTOR_TYPE_STRAIGHT;

    /**
     * Line Weight.
     *
     * @var int
     */
    private $weight;

    /**
     * Line color.
     *
     * @var string
     */
    private $color;

    /**
     * Dash style.
     *
     * @var string
     */
    private $dash;

    /**
     * Begin arrow.
     *
     * @var string
     */
    private $beginArrow;

    /**
     * End arrow.
     *
     * @var string
     */
    private $endArrow;

    /**
     * Get flip.
     *
     * @return bool
     */
    public function isFlip()
    {
        return $this->flip;
    }

    /**
     * Set flip.
     *
     * @param bool $value
     *
     * @return self
     */
    public function setFlip($value = false)
    {
        $this->flip = $this->setBoolVal($value, $this->flip);

        return $this;
    }

    /**
     * Get connectorType.
     *
     * @return string
     */
    public function getConnectorType()
    {
        return $this->connectorType;
    }

    /**
     * Set connectorType.
     *
     * @param string $value
     *
     * @return self
     */
    public function setConnectorType($value = null)
    {
        $enum = [
            self::CONNECTOR_TYPE_STRAIGHT,
        ];
        $this->connectorType = $this->setEnumVal($value, $enum, $this->connectorType);

        return $this;
    }

    /**
     * Get weight.
     *
     * @return int
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set weight.
     *
     * @param int $value Weight in points
     *
     * @return self
     */
    public function setWeight($value = null)
    {
        $this->weight = $this->setNumericVal($value, $this->weight);

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
     * Get beginArrow.
     *
     * @return string
     */
    public function getBeginArrow()
    {
        return $this->beginArrow;
    }

    /**
     * Set beginArrow.
     *
     * @param string $value
     *
     * @return self
     */
    public function setBeginArrow($value = null)
    {
        $enum = [
            self::ARROW_STYLE_BLOCK, self::ARROW_STYLE_CLASSIC, self::ARROW_STYLE_DIAMOND,
            self::ARROW_STYLE_OPEN, self::ARROW_STYLE_OVAL,
        ];
        $this->beginArrow = $this->setEnumVal($value, $enum, $this->beginArrow);

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
     * Set endArrow.
     *
     * @param string $value
     *
     * @return self
     */
    public function setEndArrow($value = null)
    {
        $enum = [
            self::ARROW_STYLE_BLOCK, self::ARROW_STYLE_CLASSIC, self::ARROW_STYLE_DIAMOND,
            self::ARROW_STYLE_OPEN, self::ARROW_STYLE_OVAL,
        ];
        $this->endArrow = $this->setEnumVal($value, $enum, $this->endArrow);

        return $this;
    }

    /**
     * Get Dash.
     *
     * @return string
     */
    public function getDash()
    {
        return $this->dash;
    }

    /**
     * Set Dash.
     *
     * @param string $value
     *
     * @return self
     */
    public function setDash($value = null)
    {
        $enum = [
            self::DASH_STYLE_DASH, self::DASH_STYLE_DASH_DOT, self::DASH_STYLE_LONG_DASH,
            self::DASH_STYLE_LONG_DASH_DOT, self::DASH_STYLE_LONG_DASH_DOT_DOT, self::DASH_STYLE_ROUND_DOT,
            self::DASH_STYLE_SQUARE_DOT,
        ];
        $this->dash = $this->setEnumVal($value, $enum, $this->dash);

        return $this;
    }
}
