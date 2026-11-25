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

namespace PhpOffice\PhpWord\Writer\Word2007\Element;

use PhpOffice\PhpWord\Element\Line as LineElement;
use PhpOffice\PhpWord\Writer\Word2007\Style\Line as LineStyleWriter;

/**
 * Line element writer.
 */
class Line extends AbstractElement
{
    /**
     * Write element.
     */
    public function write(): void
    {
        $xmlWriter = $this->getXmlWriter();
        $element = $this->getElement();
        if (!$element instanceof LineElement) {
            return;
        }

        $style = $element->getStyle();
        $styleWriter = new LineStyleWriter($xmlWriter, $style);

        $elementId = $element->getElementIndex();

        if (!$this->withoutP) {
            $xmlWriter->startElement('w:p');
            $styleWriter->writeAlignment();
        }
        $this->writeCommentRangeStart();

        $xmlWriter->startElement('w:r');
        $xmlWriter->startElement('w:pict');

        // Shapetype could be defined for each line separately, but then a unique id would be necessary
        if ($elementId == 1) {
            $xmlWriter->startElement('v:shapetype');
            $xmlWriter->writeAttribute('id', '_x0000_t32');
            $xmlWriter->writeAttribute('coordsize', '21600,21600');
            $xmlWriter->writeAttribute('o:spt', '32');
            $xmlWriter->writeAttribute('o:oned', 't');
            $xmlWriter->writeAttribute('path', 'm,l21600,21600e');
            $xmlWriter->writeAttribute('filled', 'f');
            $xmlWriter->startElement('v:path');
            $xmlWriter->writeAttribute('arrowok', 't');
            $xmlWriter->writeAttribute('fillok', 'f');
            $xmlWriter->writeAttribute('o:connecttype', 'none');
            $xmlWriter->endElement(); // v:path
            $xmlWriter->startElement('o:lock');
            $xmlWriter->writeAttribute('v:ext', 'edit');
            $xmlWriter->writeAttribute('shapetype', 't');
            $xmlWriter->endElement(); // o:lock
            $xmlWriter->endElement(); // v:shapetype
        }

        $xmlWriter->startElement('v:shape');
        $xmlWriter->writeAttribute('id', sprintf('_x0000_s1%1$03d', $elementId));
        $xmlWriter->writeAttribute('type', '#_x0000_t32'); //type should correspond to shapetype id

        $styleWriter->write();
        $styleWriter->writeStroke();

        $xmlWriter->endElement(); // v:shape

        $xmlWriter->endElement(); // w:pict
        $xmlWriter->endElement(); // w:r

        $this->endElementP(); // w:p
    }
}
