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
 * TextRun element writer.
 *
 * @since 0.10.0
 */
class Title extends AbstractElement
{
    /**
     * Write title element.
     */
    public function write(): void
    {
        $xmlWriter = $this->getXmlWriter();
        $element = $this->getElement();
        if (!$element instanceof \PhpOffice\PhpWord\Element\Title) {
            return;
        }

        $style = $element->getStyle();

        $xmlWriter->startElement('w:p');

        if (!empty($style)) {
            $xmlWriter->startElement('w:pPr');
            $xmlWriter->startElement('w:pStyle');
            $xmlWriter->writeAttribute('w:val', $style);
            $xmlWriter->endElement();
            $xmlWriter->endElement();
        }

        $bookmarkRId = null;
        if ($element->getDepth() !== 0) {
            $rId = $element->getRelationId();
            $bookmarkRId = $element->getPhpWord()->addBookmark();

            // Bookmark start for TOC
            $xmlWriter->startElement('w:bookmarkStart');
            $xmlWriter->writeAttribute('w:id', $bookmarkRId);
            $xmlWriter->writeAttribute('w:name', "_Toc{$rId}");
            $xmlWriter->endElement(); //w:bookmarkStart
        }

        // Actual text
        $text = $element->getText();
        if (is_string($text)) {
            $xmlWriter->startElement('w:r');
            $xmlWriter->startElement('w:t');
            $this->writeText($text);
            $xmlWriter->endElement(); // w:t
            $xmlWriter->endElement(); // w:r
        }
        if ($text instanceof \PhpOffice\PhpWord\Element\AbstractContainer) {
            $containerWriter = new Container($xmlWriter, $text);
            $containerWriter->write();
        }

        if ($element->getDepth() !== 0) {
            // Bookmark end
            $xmlWriter->startElement('w:bookmarkEnd');
            $xmlWriter->writeAttribute('w:id', $bookmarkRId);
            $xmlWriter->endElement(); //w:bookmarkEnd
        }
        $xmlWriter->endElement(); //w:p
    }
}
