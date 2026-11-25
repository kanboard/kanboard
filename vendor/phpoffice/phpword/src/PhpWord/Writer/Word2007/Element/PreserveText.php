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
 * PreserveText element writer.
 *
 * @since 0.10.0
 */
class PreserveText extends Text
{
    /**
     * Write preserve text element.
     */
    public function write(): void
    {
        $xmlWriter = $this->getXmlWriter();
        $element = $this->getElement();
        if (!$element instanceof \PhpOffice\PhpWord\Element\PreserveText) {
            return;
        }

        $texts = $element->getText();
        if (!is_array($texts)) {
            $texts = [$texts];
        }

        $this->startElementP();

        foreach ($texts as $text) {
            if (substr($text, 0, 1) == '{') {
                $text = substr($text, 1, -1);

                $xmlWriter->startElement('w:r');
                $xmlWriter->startElement('w:fldChar');
                $xmlWriter->writeAttribute('w:fldCharType', 'begin');
                $xmlWriter->endElement();
                $xmlWriter->endElement();

                $xmlWriter->startElement('w:r');

                $this->writeFontStyle();

                $xmlWriter->startElement('w:instrText');
                $xmlWriter->writeAttribute('xml:space', 'preserve');
                $this->writeText($text);
                $xmlWriter->endElement();
                $xmlWriter->endElement();

                $xmlWriter->startElement('w:r');
                $xmlWriter->startElement('w:fldChar');
                $xmlWriter->writeAttribute('w:fldCharType', 'separate');
                $xmlWriter->endElement();
                $xmlWriter->endElement();

                $xmlWriter->startElement('w:r');
                $xmlWriter->startElement('w:fldChar');
                $xmlWriter->writeAttribute('w:fldCharType', 'end');
                $xmlWriter->endElement();
                $xmlWriter->endElement();
            } else {
                $xmlWriter->startElement('w:r');

                $this->writeFontStyle();

                $xmlWriter->startElement('w:t');
                $xmlWriter->writeAttribute('xml:space', 'preserve');
                $this->writeText($this->getText($text));
                $xmlWriter->endElement();
                $xmlWriter->endElement();
            }
        }

        $this->endElementP(); // w:p
    }
}
