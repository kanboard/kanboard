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
use PhpOffice\PhpWord\SimpleType\NumberFormat;

/**
 * Numbering level definition.
 *
 * @see  http://www.schemacentral.com/sc/ooxml/e-w_lvl-1.html
 * @since 0.10.0
 */
class NumberingLevel extends AbstractStyle
{
    /**
     * Level number, 0 to 8 (total 9 levels).
     *
     * @var int
     */
    private $level = 0;

    /**
     * Starting value w:start.
     *
     * @var int
     *
     * @see  http://www.schemacentral.com/sc/ooxml/e-w_start-1.html
     */
    private $start = 1;

    /**
     * Numbering format w:numFmt, one of PhpOffice\PhpWord\SimpleType\NumberFormat.
     *
     * @var string
     *
     * @see  http://www.schemacentral.com/sc/ooxml/t-w_ST_NumberFormat.html
     */
    private $format;

    /**
     * Restart numbering level symbol w:lvlRestart.
     *
     * @var int
     *
     * @see  http://www.schemacentral.com/sc/ooxml/e-w_lvlRestart-1.html
     */
    private $restart;

    /**
     * Related paragraph style.
     *
     * @var string
     *
     * @see  http://www.schemacentral.com/sc/ooxml/e-w_pStyle-2.html
     */
    private $pStyle;

    /**
     * Content between numbering symbol and paragraph text w:suff.
     *
     * @var string tab|space|nothing
     *
     * @see  http://www.schemacentral.com/sc/ooxml/e-w_suff-1.html
     */
    private $suffix = 'tab';

    /**
     * Numbering level text e.g. %1 for nonbullet or bullet character.
     *
     * @var string
     *
     * @see  http://www.schemacentral.com/sc/ooxml/e-w_lvlText-1.html
     */
    private $text;

    /**
     * Justification, w:lvlJc.
     *
     * @var string, one of PhpOffice\PhpWord\SimpleType\Jc
     */
    private $alignment = '';

    /**
     * Left.
     *
     * @var int
     */
    private $left;

    /**
     * Hanging.
     *
     * @var int
     */
    private $hanging;

    /**
     * Tab position.
     *
     * @var int
     */
    private $tabPos;

    /**
     * Font family.
     *
     * @var string
     */
    private $font;

    /**
     * Hint default|eastAsia|cs.
     *
     * @var string
     *
     * @see  http://www.schemacentral.com/sc/ooxml/a-w_hint-1.html
     */
    private $hint;

    /**
     * Get level.
     *
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set level.
     *
     * @param int $value
     *
     * @return self
     */
    public function setLevel($value)
    {
        $this->level = $this->setIntVal($value, $this->level);

        return $this;
    }

    /**
     * Get start.
     *
     * @return int
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set start.
     *
     * @param int $value
     *
     * @return self
     */
    public function setStart($value)
    {
        $this->start = $this->setIntVal($value, $this->start);

        return $this;
    }

    /**
     * Get format.
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Set format.
     *
     * @param string $value
     *
     * @return self
     */
    public function setFormat($value)
    {
        $this->format = $this->setEnumVal($value, NumberFormat::values(), $this->format);

        return $this;
    }

    /**
     * Get restart.
     *
     * @return int
     */
    public function getRestart()
    {
        return $this->restart;
    }

    /**
     * Set restart.
     *
     * @param int $value
     *
     * @return self
     */
    public function setRestart($value)
    {
        $this->restart = $this->setIntVal($value, $this->restart);

        return $this;
    }

    /**
     * Get related paragraph style.
     *
     * @return string
     */
    public function getPStyle()
    {
        return $this->pStyle;
    }

    /**
     * Set  related paragraph style.
     *
     * @param string $value
     *
     * @return self
     */
    public function setPStyle($value)
    {
        $this->pStyle = $value;

        return $this;
    }

    /**
     * Get suffix.
     *
     * @return string
     */
    public function getSuffix()
    {
        return $this->suffix;
    }

    /**
     * Set suffix.
     *
     * @param string $value
     *
     * @return self
     */
    public function setSuffix($value)
    {
        $enum = ['tab', 'space', 'nothing'];
        $this->suffix = $this->setEnumVal($value, $enum, $this->suffix);

        return $this;
    }

    /**
     * Get text.
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set text.
     *
     * @param string $value
     *
     * @return self
     */
    public function setText($value)
    {
        $this->text = $value;

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
        if (Jc::isValid($value)) {
            $this->alignment = $value;
        }

        return $this;
    }

    /**
     * Get left.
     *
     * @return int
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * Set left.
     *
     * @param int $value
     *
     * @return self
     */
    public function setLeft($value)
    {
        $this->left = $this->setIntVal($value, $this->left);

        return $this;
    }

    /**
     * Get hanging.
     *
     * @return int
     */
    public function getHanging()
    {
        return $this->hanging;
    }

    /**
     * Set hanging.
     *
     * @param int $value
     *
     * @return self
     */
    public function setHanging($value)
    {
        $this->hanging = $this->setIntVal($value, $this->hanging);

        return $this;
    }

    /**
     * Get tab.
     *
     * @return int
     */
    public function getTabPos()
    {
        return $this->tabPos;
    }

    /**
     * Set tab.
     *
     * @param int $value
     *
     * @return self
     */
    public function setTabPos($value)
    {
        $this->tabPos = $this->setIntVal($value, $this->tabPos);

        return $this;
    }

    /**
     * Get font.
     *
     * @return string
     */
    public function getFont()
    {
        return $this->font;
    }

    /**
     * Set font.
     *
     * @param string $value
     *
     * @return self
     */
    public function setFont($value)
    {
        $this->font = $value;

        return $this;
    }

    /**
     * Get hint.
     *
     * @return string
     */
    public function getHint()
    {
        return $this->hint;
    }

    /**
     * Set hint.
     *
     * @param string $value
     *
     * @return self
     */
    public function setHint($value = null)
    {
        $enum = ['default', 'eastAsia', 'cs'];
        $this->hint = $this->setEnumVal($value, $enum, $this->hint);

        return $this;
    }
}
