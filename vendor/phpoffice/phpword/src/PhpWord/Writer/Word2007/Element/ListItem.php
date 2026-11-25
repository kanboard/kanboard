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

use PhpOffice\PhpWord\Writer\Word2007\Style\Paragraph as ParagraphStyleWriter;

/**
 * ListItem element writer.
 *
 * @since 0.10.0
 */
class ListItem extends AbstractElement
{
    /**
     * Write list item element.
     */
    public function write(): void
    {
        $xmlWriter = $this->getXmlWriter();
        $element = $this->getElement();
        if (!$element instanceof \PhpOffice\PhpWord\Element\ListItem) {
            return;
        }

        $textObject = $element->getTextObject();

        $styleWriter = new ParagraphStyleWriter($xmlWriter, $textObject->getParagraphStyle());
        $styleWriter->setWithoutPPR(true);
        $styleWriter->setIsInline(true);

        $xmlWriter->startElement('w:p');

        $xmlWriter->startElement('w:pPr');
        $styleWriter->write();

        $xmlWriter->startElement('w:numPr');
        $xmlWriter->startElement('w:ilvl');
        $xmlWriter->writeAttribute('w:val', $element->getDepth());
        $xmlWriter->endElement(); // w:ilvl
        $xmlWriter->startElement('w:numId');
        $xmlWriter->writeAttribute('w:val', $element->getStyle()->getNumId());
        $xmlWriter->endElement(); // w:numId
        $xmlWriter->endElement(); // w:numPr

        $xmlWriter->endElement(); // w:pPr

        $elementWriter = new Text($xmlWriter, $textObject, true);
        $elementWriter->write();

        $xmlWriter->endElement(); // w:p
    }
}
