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

use PhpOffice\PhpWord\Element\Image as ImageElement;
use PhpOffice\PhpWord\Shared\XMLWriter;
use PhpOffice\PhpWord\Style\Font as FontStyle;
use PhpOffice\PhpWord\Style\Frame as FrameStyle;
use PhpOffice\PhpWord\Writer\Word2007\Style\Font as FontStyleWriter;
use PhpOffice\PhpWord\Writer\Word2007\Style\Image as ImageStyleWriter;

/**
 * Image element writer.
 *
 * @since 0.10.0
 */
class Image extends AbstractElement
{
    /**
     * Write element.
     */
    public function write(): void
    {
        $xmlWriter = $this->getXmlWriter();
        $element = $this->getElement();
        if (!$element instanceof ImageElement) {
            return;
        }

        if ($element->isWatermark()) {
            $this->writeWatermark($xmlWriter, $element);
        } else {
            $this->writeImage($xmlWriter, $element);
        }
    }

    /**
     * Write image element.
     */
    private function writeImage(XMLWriter $xmlWriter, ImageElement $element): void
    {
        $rId = $element->getRelationId() + ($element->isInSection() ? 6 : 0);
        $style = $element->getStyle();
        $styleWriter = new ImageStyleWriter($xmlWriter, $style);

        if (!$this->withoutP) {
            $xmlWriter->startElement('w:p');
            $styleWriter->writeAlignment();
        }
        $this->writeCommentRangeStart();

        $xmlWriter->startElement('w:r');

        // Write position
        $position = $style->getPosition();
        if ($position && $style->getWrap() == FrameStyle::WRAP_INLINE) {
            $fontStyle = new FontStyle('text');
            $fontStyle->setPosition($position);
            $fontStyleWriter = new FontStyleWriter($xmlWriter, $fontStyle);
            $fontStyleWriter->write();
        }

        $xmlWriter->startElement('w:pict');
        $xmlWriter->startElement('v:shape');
        $xmlWriter->writeAttribute('type', '#_x0000_t75');
        $xmlWriter->writeAttribute('stroked', 'f');

        $styleWriter->write();

        $xmlWriter->startElement('v:imagedata');
        $xmlWriter->writeAttribute('r:id', 'rId' . $rId);
        $xmlWriter->writeAttribute('o:title', '');
        $xmlWriter->endElement(); // v:imagedata

        $xmlWriter->endElement(); // v:shape
        $xmlWriter->endElement(); // w:pict
        $xmlWriter->endElement(); // w:r

        $this->endElementP();
    }

    /**
     * Write watermark element.
     */
    private function writeWatermark(XMLWriter $xmlWriter, ImageElement $element): void
    {
        $rId = $element->getRelationId();
        $style = $element->getStyle();
        $style->setPositioning('absolute');
        $styleWriter = new ImageStyleWriter($xmlWriter, $style);

        if (!$this->withoutP) {
            $xmlWriter->startElement('w:p');
        }
        $xmlWriter->startElement('w:r');
        $xmlWriter->startElement('w:pict');
        $xmlWriter->startElement('v:shape');
        $xmlWriter->writeAttribute('type', '#_x0000_t75');
        $xmlWriter->writeAttribute('stroked', 'f');

        $styleWriter->write();

        $xmlWriter->startElement('v:imagedata');
        $xmlWriter->writeAttribute('r:id', 'rId' . $rId);
        $xmlWriter->writeAttribute('o:title', '');
        $xmlWriter->endElement(); // v:imagedata
        $xmlWriter->endElement(); // v:shape
        $xmlWriter->endElement(); // w:pict
        $xmlWriter->endElement(); // w:r
        if (!$this->withoutP) {
            $xmlWriter->endElement(); // w:p
        }
    }
}
