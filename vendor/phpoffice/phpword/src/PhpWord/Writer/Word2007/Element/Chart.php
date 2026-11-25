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

use PhpOffice\PhpWord\Element\Chart as ChartElement;

/**
 * Chart element writer.
 *
 * @since 0.12.0
 */
class Chart extends AbstractElement
{
    /**
     * Write element.
     */
    public function write(): void
    {
        $xmlWriter = $this->getXmlWriter();
        $element = $this->getElement();
        if (!$element instanceof ChartElement) {
            return;
        }

        $rId = $element->getRelationId();
        $style = $element->getStyle();

        if (!$this->withoutP) {
            $xmlWriter->startElement('w:p');
        }
        $this->writeCommentRangeStart();

        $xmlWriter->startElement('w:r');
        $xmlWriter->startElement('w:drawing');
        $xmlWriter->startElement('wp:inline');

        // EMU
        $xmlWriter->writeElementBlock('wp:extent', ['cx' => $style->getWidth(), 'cy' => $style->getHeight()]);
        $xmlWriter->writeElementBlock('wp:docPr', ['id' => $rId, 'name' => "Chart{$rId}"]);

        $xmlWriter->startElement('a:graphic');
        $xmlWriter->writeAttribute('xmlns:a', 'http://schemas.openxmlformats.org/drawingml/2006/main');
        $xmlWriter->startElement('a:graphicData');
        $xmlWriter->writeAttribute('uri', 'http://schemas.openxmlformats.org/drawingml/2006/chart');

        $xmlWriter->startElement('c:chart');
        $xmlWriter->writeAttribute('r:id', "rId{$rId}");
        $xmlWriter->writeAttribute('xmlns:c', 'http://schemas.openxmlformats.org/drawingml/2006/chart');
        $xmlWriter->writeAttribute('xmlns:r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');
        $xmlWriter->endElement(); // c:chart

        $xmlWriter->endElement(); // a:graphicData
        $xmlWriter->endElement(); // a:graphic

        $xmlWriter->endElement(); // wp:inline
        $xmlWriter->endElement(); // w:drawing
        $xmlWriter->endElement(); // w:r

        $this->endElementP(); // w:p
    }
}
