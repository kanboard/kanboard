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
 * Spacing between lines and above/below paragraph style writer.
 *
 * @since 0.10.0
 */
class Spacing extends AbstractStyle
{
    /**
     * Write style.
     */
    public function write(): void
    {
        $style = $this->getStyle();
        if (!$style instanceof \PhpOffice\PhpWord\Style\Spacing) {
            return;
        }
        $xmlWriter = $this->getXmlWriter();

        $xmlWriter->startElement('w:spacing');

        $before = $style->getBefore();
        $xmlWriter->writeAttributeIf(null !== $before, 'w:before', $this->convertTwip($before));

        $after = $style->getAfter();
        $xmlWriter->writeAttributeIf(null !== $after, 'w:after', $this->convertTwip($after));

        $line = $style->getLine();
        //if linerule is auto, the spacing is supposed to include the height of the line itself, which is 240 twips
        if (null !== $line && 'auto' === $style->getLineRule()) {
            $line += \PhpOffice\PhpWord\Style\Paragraph::LINE_HEIGHT;
        }
        $xmlWriter->writeAttributeIf(null !== $line, 'w:line', $line);

        $xmlWriter->writeAttributeIf(null !== $line, 'w:lineRule', $style->getLineRule());

        $xmlWriter->endElement();
    }
}
