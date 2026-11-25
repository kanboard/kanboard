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

namespace PhpOffice\PhpWord\Writer\ODText\Style;

use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Style\Numbering as StyleNumbering;

/**
 * Numbering style writer.
 */
class Numbering extends AbstractStyle
{
    /**
     * Write style.
     */
    public function write(): void
    {
        /** @var StyleNumbering $style Type hint */
        $style = $this->getStyle();
        if (!$style instanceof StyleNumbering) {
            return;
        }
        $xmlWriter = $this->getXmlWriter();

        $xmlWriter->startElement('text:list-style');
        $xmlWriter->writeAttribute('style:name', $style->getStyleName());

        foreach ($style->getLevels() as $styleLevel) {
            $numLevel = $styleLevel->getLevel() + 1;

            // In Twips
            $tabPos = $styleLevel->getTabPos();
            // In Inches
            $tabPos /= Converter::INCH_TO_TWIP;
            // In Centimeters
            $tabPos *= Converter::INCH_TO_CM;

            // In Twips
            $hanging = $styleLevel->getHanging();
            // In Inches
            $hanging /= Converter::INCH_TO_TWIP;
            // In Centimeters
            $hanging *= Converter::INCH_TO_CM;

            $xmlWriter->startElement('text:list-level-style-bullet');
            $xmlWriter->writeAttribute('text:level', $numLevel);
            $xmlWriter->writeAttribute('text:style-name', $style->getStyleName() . '_' . $numLevel);
            $xmlWriter->writeAttribute('text:bullet-char', $styleLevel->getText());

            $xmlWriter->startElement('style:list-level-properties');
            $xmlWriter->writeAttribute('text:list-level-position-and-space-mode', 'label-alignment');

            $xmlWriter->startElement('style:list-level-label-alignment');
            $xmlWriter->writeAttribute('text:label-followed-by', 'listtab');
            $xmlWriter->writeAttribute('text:list-tab-stop-position', number_format($tabPos, 2, '.', '') . 'cm');
            $xmlWriter->writeAttribute('fo:text-indent', '-' . number_format($hanging, 2, '.', '') . 'cm');
            $xmlWriter->writeAttribute('fo:margin-left', number_format($tabPos, 2, '.', '') . 'cm');

            $xmlWriter->endElement(); // style:list-level-label-alignment
            $xmlWriter->endElement(); // style:list-level-properties

            $xmlWriter->startElement('style:text-properties');
            $xmlWriter->writeAttribute('style:font-name', $styleLevel->getFont());
            $xmlWriter->endElement(); // style:text-properties

            $xmlWriter->endElement(); // text:list-level-style-bullet
        }

        $xmlWriter->endElement(); // text:list-style
    }
}
