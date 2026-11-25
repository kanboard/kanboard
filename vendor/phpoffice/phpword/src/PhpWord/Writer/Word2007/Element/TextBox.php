<?php

/**
 * This file is part of PHPWord - A pure PHP library for reading and writing
 * word processing documents.
 * PHPWord is free software distributed under the terms of the GNU Lesser
 * General Public License version 3 as published by the Free Software Foundation.
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code. For the full list of
 * contributors, visit https://github.com/PHPOffice/PHPWord/contributors.
 *
 * @see         https://github.com/PHPOffice/PHPWord
 *
 * @license     http://www.gnu.org/licenses/lgpl.txt LGPL version 3
 */

namespace PhpOffice\PhpWord\Writer\Word2007\Element;

use PhpOffice\PhpWord\Writer\Word2007\Style\TextBox as TextBoxStyleWriter;

/**
 * TextBox element writer.
 *
 * @since 0.11.0
 */
class TextBox extends Image
{
    /**
     * Write element.
     */
    public function write(): void
    {
        $xmlWriter = $this->getXmlWriter();
        $element = $this->getElement();
        if (!$element instanceof \PhpOffice\PhpWord\Element\TextBox) {
            return;
        }
        $style = $element->getStyle();
        $styleWriter = new TextBoxStyleWriter($xmlWriter, $style);

        if (!$this->withoutP) {
            $xmlWriter->startElement('w:p');
            $styleWriter->writeAlignment();
        }
        $this->writeCommentRangeStart();

        $xmlWriter->startElement('w:r');
        $xmlWriter->startElement('w:pict');
        $xmlWriter->startElement('v:shape');
        $xmlWriter->writeAttribute('type', '#_x0000_t0202');

        if ($style->getBgColor()) {
            $xmlWriter->writeAttribute('fillcolor', $style->getBgColor());
        } else {
            $xmlWriter->writeAttribute('filled', 'f');
        }

        if (!$style->getBorderColor()) {
            $xmlWriter->writeAttribute('stroked', 'f');
            $xmlWriter->writeAttribute('strokecolor', 'white');
        }

        $styleWriter->write();
        $styleWriter->writeBorder();

        $xmlWriter->startElement('v:textbox');
        $styleWriter->writeInnerMargin();

        // TextBox content, serving as a container
        $xmlWriter->startElement('w:txbxContent');
        $containerWriter = new Container($xmlWriter, $element);
        $containerWriter->write();
        $xmlWriter->endElement(); // w:txbxContent

        $xmlWriter->endElement(); // v: textbox

        $xmlWriter->endElement(); // v:shape
        $xmlWriter->endElement(); // w:pict
        $xmlWriter->endElement(); // w:r

        $this->endElementP();
    }
}
