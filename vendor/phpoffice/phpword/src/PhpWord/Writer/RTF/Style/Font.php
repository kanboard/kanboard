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

namespace PhpOffice\PhpWord\Writer\RTF\Style;

use PhpOffice\PhpWord\Style\Font as FontStyle;

/**
 * RTF font style writer.
 *
 * @since 0.11.0
 */
class Font extends AbstractStyle
{
    /**
     * @var int Font name index
     */
    private $nameIndex = 0;

    /**
     * @var int Font color index
     */
    private $colorIndex = 0;

    /**
     * Write style.
     *
     * @return string
     */
    public function write()
    {
        $style = $this->getStyle();
        if (!$style instanceof FontStyle) {
            return '';
        }

        $content = '';
        $content .= $this->getValueIf($style->isRTL(), '\rtlch');
        $content .= '\cf' . $this->colorIndex;
        $content .= '\f' . $this->nameIndex;

        $size = $style->getSize();
        $content .= $this->getValueIf(is_numeric($size), '\fs' . round($size * 2));

        $content .= $this->getValueIf($style->isBold(), '\b');
        $content .= $this->getValueIf($style->isItalic(), '\i');
        $content .= $this->getValueIf($style->getUnderline() != FontStyle::UNDERLINE_NONE, '\ul');
        $content .= $this->getValueIf($style->isStrikethrough(), '\strike');
        $content .= $this->getValueIf($style->isSuperScript(), '\super');
        $content .= $this->getValueIf($style->isSubScript(), '\sub');

        return $content . ' ';
    }

    /**
     * Set font name index.
     *
     * @param int $value
     */
    public function setNameIndex($value = 0): void
    {
        $this->nameIndex = $value;
    }

    /**
     * Set font color index.
     *
     * @param int $value
     */
    public function setColorIndex($value = 0): void
    {
        $this->colorIndex = $value;
    }
}
