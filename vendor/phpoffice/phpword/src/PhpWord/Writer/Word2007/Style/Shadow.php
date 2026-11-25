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
 * Shadow style writer.
 *
 * @since 0.12.0
 */
class Shadow extends AbstractStyle
{
    /**
     * Write style.
     */
    public function write(): void
    {
        $style = $this->getStyle();
        if (!$style instanceof \PhpOffice\PhpWord\Style\Shadow) {
            return;
        }
        $xmlWriter = $this->getXmlWriter();

        $xmlWriter->startElement('v:shadow');
        $xmlWriter->writeAttribute('on', 't');
        $xmlWriter->writeAttributeIf($style->getColor() !== null, 'color', $style->getColor());
        $xmlWriter->writeAttributeIf($style->getOffset() !== null, 'offset', $style->getOffset());
        $xmlWriter->endElement();
    }
}
