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

namespace PhpOffice\PhpWord\Writer\ODText\Element;

use PhpOffice\PhpWord\Element\Image as ElementImage;
use PhpOffice\PhpWord\Shared\Converter;

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
        $element = $this->getElement();
        if (!$element instanceof ElementImage) {
            return;
        }

        $mediaIndex = $element->getMediaIndex();
        $target = 'Pictures/' . $element->getTarget();
        $style = $element->getStyle();
        $width = Converter::pixelToCm($style->getWidth());
        $height = Converter::pixelToCm($style->getHeight());

        $xmlWriter = $this->getXmlWriter();

        if (!$this->withoutP) {
            $xmlWriter->startElement('text:p');
            $xmlWriter->writeAttribute('text:style-name', 'IM' . $mediaIndex);
        }

        $xmlWriter->startElement('draw:frame');
        $xmlWriter->writeAttribute('draw:style-name', 'fr' . $mediaIndex);
        $xmlWriter->writeAttributeIf($this->withoutP, 'draw:text-style-name', 'IM' . $mediaIndex);
        $xmlWriter->writeAttribute('draw:name', $element->getElementId());
        $xmlWriter->writeAttribute('text:anchor-type', 'as-char');
        $xmlWriter->writeAttribute('svg:width', $width . 'cm');
        $xmlWriter->writeAttribute('svg:height', $height . 'cm');
        $xmlWriter->writeAttribute('draw:z-index', $mediaIndex);

        $xmlWriter->startElement('draw:image');
        $xmlWriter->writeAttribute('xlink:href', $target);
        $xmlWriter->writeAttribute('xlink:type', 'simple');
        $xmlWriter->writeAttribute('xlink:show', 'embed');
        $xmlWriter->writeAttribute('xlink:actuate', 'onLoad');
        $xmlWriter->endElement(); // draw:image

        $xmlWriter->endElement(); // draw:frame

        if (!$this->withoutP) {
            $xmlWriter->endElement(); // text:p
        }
    }
}
