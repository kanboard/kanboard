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

use PhpOffice\PhpWord\Writer\Word2007\Style\Image as ImageStyleWriter;

/**
 * OLEObject element writer.
 *
 * @since 0.10.0
 */
class OLEObject extends AbstractElement
{
    /**
     * Write object element.
     */
    public function write(): void
    {
        $xmlWriter = $this->getXmlWriter();
        $element = $this->getElement();
        if (!$element instanceof \PhpOffice\PhpWord\Element\OLEObject) {
            return;
        }

        $rIdObject = $element->getRelationId() + ($element->isInSection() ? 6 : 0);
        $rIdImage = $element->getImageRelationId() + ($element->isInSection() ? 6 : 0);
        $shapeId = md5($rIdObject . '_' . $rIdImage);
        $objectId = $element->getRelationId() + 1325353440;

        $style = $element->getStyle();
        $styleWriter = new ImageStyleWriter($xmlWriter, $style);

        if (!$this->withoutP) {
            $xmlWriter->startElement('w:p');
            $styleWriter->writeAlignment();
        }
        $this->writeCommentRangeStart();

        $xmlWriter->startElement('w:r');
        $xmlWriter->startElement('w:object');
        $xmlWriter->writeAttribute('w:dxaOrig', '249');
        $xmlWriter->writeAttribute('w:dyaOrig', '160');

        // Icon
        $xmlWriter->startElement('v:shape');
        $xmlWriter->writeAttribute('id', $shapeId);
        $xmlWriter->writeAttribute('type', '#_x0000_t75');
        $xmlWriter->writeAttribute('style', 'width:104px;height:67px');
        $xmlWriter->writeAttribute('o:ole', '');

        $xmlWriter->startElement('v:imagedata');
        $xmlWriter->writeAttribute('r:id', 'rId' . $rIdImage);
        $xmlWriter->writeAttribute('o:title', '');
        $xmlWriter->endElement(); // v:imagedata

        $xmlWriter->endElement(); // v:shape

        // Object
        $xmlWriter->startElement('o:OLEObject');
        $xmlWriter->writeAttribute('Type', 'Embed');
        $xmlWriter->writeAttribute('ProgID', 'Package');
        $xmlWriter->writeAttribute('ShapeID', $shapeId);
        $xmlWriter->writeAttribute('DrawAspect', 'Icon');
        $xmlWriter->writeAttribute('ObjectID', '_' . $objectId);
        $xmlWriter->writeAttribute('r:id', 'rId' . $rIdObject);
        $xmlWriter->endElement(); // o:OLEObject

        $xmlWriter->endElement(); // w:object
        $xmlWriter->endElement(); // w:r

        $this->endElementP(); // w:p
    }
}
