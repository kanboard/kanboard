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

/**
 * Image style writer.
 *
 * @since 0.11.0
 */
class Image extends AbstractStyle
{
    /**
     * Write style.
     */
    public function write(): void
    {
        /** @var \PhpOffice\PhpWord\Style\Image $style Type hint */
        $style = $this->getStyle();
        if (!$style instanceof \PhpOffice\PhpWord\Style\Image) {
            return;
        }
        $xmlWriter = $this->getXmlWriter();

        $xmlWriter->startElement('style:style');
        $xmlWriter->writeAttribute('style:name', $style->getStyleName());
        $xmlWriter->writeAttribute('style:family', 'graphic');
        $xmlWriter->writeAttribute('style:parent-style-name', 'Graphics');
        $xmlWriter->startElement('style:graphic-properties');
        $xmlWriter->writeAttribute('style:vertical-pos', 'top');
        $xmlWriter->writeAttribute('style:vertical-rel', 'baseline');
        $xmlWriter->endElement(); // style:graphic-properties
        $xmlWriter->endElement(); // style:style
    }
}
