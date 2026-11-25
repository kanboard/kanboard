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

/**
 * Ruby element writer.
 */
class Ruby extends AbstractElement
{
    /**
     * Write ruby element.
     */
    public function write(): void
    {
        $xmlWriter = $this->getXmlWriter();
        $element = $this->getElement();
        if (!$element instanceof \PhpOffice\PhpWord\Element\Ruby) {
            return;
        }
        /** @var \PhpOffice\PhpWord\Element\Ruby $element */
        $this->startElementP();

        $xmlWriter->startElement('w:r');
        $xmlWriter->startElement('w:ruby');

        // write properties
        $xmlWriter->startElement('w:rubyPr');
        $properties = $element->getProperties();
        $xmlWriter->startElement('w:rubyAlign');
        $xmlWriter->writeAttribute('w:val', $properties->getAlignment());
        $xmlWriter->endElement(); // w:rubyAlign
        $xmlWriter->startElement('w:hps');
        $xmlWriter->writeAttribute('w:val', $properties->getFontFaceSize());
        $xmlWriter->endElement(); // w:hps
        $xmlWriter->startElement('w:hpsRaise');
        $xmlWriter->writeAttribute('w:val', $properties->getFontPointsAboveBaseText());
        $xmlWriter->endElement(); // w:hpsRaise
        $xmlWriter->startElement('w:hpsBaseText');
        $xmlWriter->writeAttribute('w:val', $properties->getFontSizeForBaseText());
        $xmlWriter->endElement(); // w:hpsBaseText
        $xmlWriter->startElement('w:lid');
        $xmlWriter->writeAttribute('w:val', $properties->getLanguageId());
        $xmlWriter->endElement(); // w:lid

        $xmlWriter->endElement(); // w:rubyPr

        // write ruby text
        $xmlWriter->startElement('w:rt');
        $rubyTextRun = $element->getRubyTextRun();
        $textRunWriter = new TextRun($xmlWriter, $rubyTextRun, true);
        $textRunWriter->write();
        $xmlWriter->endElement(); // w:rt
        // write base text
        $xmlWriter->startElement('w:rubyBase');
        $baseTextRun = $element->getBaseTextRun();
        $textRunWriter = new TextRun($xmlWriter, $baseTextRun, true);
        $textRunWriter->write();
        $xmlWriter->endElement(); // w:rubyBase

        $xmlWriter->endElement(); // w:ruby
        $xmlWriter->endElement(); // w:r

        $this->endElementP();
    }
}
