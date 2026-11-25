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
 * CheckBox element writer.
 *
 * @since 0.10.0
 */
class CheckBox extends Text
{
    /**
     * Write element.
     */
    public function write(): void
    {
        $xmlWriter = $this->getXmlWriter();
        $element = $this->getElement();
        if (!$element instanceof \PhpOffice\PhpWord\Element\CheckBox) {
            return;
        }

        $this->startElementP();

        $xmlWriter->startElement('w:r');
        $xmlWriter->startElement('w:fldChar');
        $xmlWriter->writeAttribute('w:fldCharType', 'begin');
        $xmlWriter->startElement('w:ffData');
        $xmlWriter->startElement('w:name');
        $xmlWriter->writeAttribute('w:val', $this->getText($element->getName()));
        $xmlWriter->endElement(); //w:name
        $xmlWriter->writeAttribute('w:enabled', '');
        $xmlWriter->startElement('w:calcOnExit');
        $xmlWriter->writeAttribute('w:val', '0');
        $xmlWriter->endElement(); //w:calcOnExit
        $xmlWriter->startElement('w:checkBox');
        $xmlWriter->writeAttribute('w:sizeAuto', '');
        $xmlWriter->startElement('w:default');
        $xmlWriter->writeAttribute('w:val', 0);
        $xmlWriter->endElement(); //w:default
        $xmlWriter->endElement(); //w:checkBox
        $xmlWriter->endElement(); // w:ffData
        $xmlWriter->endElement(); // w:fldChar
        $xmlWriter->endElement(); // w:r

        $xmlWriter->startElement('w:r');
        $xmlWriter->startElement('w:instrText');
        $xmlWriter->writeAttribute('xml:space', 'preserve');
        $xmlWriter->text(' FORMCHECKBOX ');
        $xmlWriter->endElement(); // w:instrText
        $xmlWriter->endElement(); // w:r
        $xmlWriter->startElement('w:r');
        $xmlWriter->startElement('w:fldChar');
        $xmlWriter->writeAttribute('w:fldCharType', 'separate');
        $xmlWriter->endElement(); // w:fldChar
        $xmlWriter->endElement(); // w:r
        $xmlWriter->startElement('w:r');
        $xmlWriter->startElement('w:fldChar');
        $xmlWriter->writeAttribute('w:fldCharType', 'end');
        $xmlWriter->endElement(); // w:fldChar
        $xmlWriter->endElement(); // w:r

        $xmlWriter->startElement('w:r');

        $this->writeFontStyle();

        $xmlWriter->startElement('w:t');
        $xmlWriter->writeAttribute('xml:space', 'preserve');
        $this->writeText($this->getText($element->getText()));
        $xmlWriter->endElement(); // w:t
        $xmlWriter->endElement(); // w:r

        $this->endElementP(); // w:p
    }
}
