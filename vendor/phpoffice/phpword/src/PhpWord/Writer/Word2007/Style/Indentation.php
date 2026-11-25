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

namespace PhpOffice\PhpWord\Writer\Word2007\Style;

/**
 * Paragraph indentation style writer.
 *
 * @since 0.10.0
 */
class Indentation extends AbstractStyle
{
    /**
     * Write style.
     */
    public function write(): void
    {
        $style = $this->getStyle();
        if (!$style instanceof \PhpOffice\PhpWord\Style\Indentation) {
            return;
        }
        $xmlWriter = $this->getXmlWriter();

        $xmlWriter->startElement('w:ind');

        $xmlWriter->writeAttribute('w:left', $this->convertTwip($style->getLeft()));
        $xmlWriter->writeAttribute('w:right', $this->convertTwip($style->getRight()));

        $firstLine = $style->getFirstLine();
        $xmlWriter->writeAttributeIf(null !== $firstLine, 'w:firstLine', $this->convertTwip($firstLine));

        $firstLineChars = $style->getFirstLineChars();
        $xmlWriter->writeAttributeIf(0 !== $firstLineChars, 'w:firstLineChars', $this->convertTwip($firstLineChars));

        $hanging = $style->getHanging();
        $xmlWriter->writeAttributeIf(null !== $hanging, 'w:hanging', $this->convertTwip($hanging));

        $xmlWriter->endElement();
    }
}
