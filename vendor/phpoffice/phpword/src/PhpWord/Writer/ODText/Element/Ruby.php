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

/**
 * Ruby element writer.
 * NOTE: This class will write out a Ruby element in the format {baseText} ({rubyText})
 * just like RTF; however, ODT files natively support Ruby text elements.
 * This implementation should be changed in the future to support ODT's native
 * Ruby elements and usage.
 */
class Ruby extends AbstractElement
{
    /**
     * Write element.
     */
    public function write(): void
    {
        $xmlWriter = $this->getXmlWriter();
        $element = $this->getElement();
        if (!$element instanceof \PhpOffice\PhpWord\Element\Ruby) {
            return;
        }
        $paragraphStyle = $element->getBaseTextRun()->getParagraphStyle();

        if (!$this->withoutP) {
            $xmlWriter->startElement('text:p'); // text:p
        }
        if (empty($paragraphStyle)) {
            if (!$this->withoutP) {
                $xmlWriter->writeAttribute('text:style-name', 'Normal');
            }
        } elseif (is_string($paragraphStyle)) {
            if (!$this->withoutP) {
                $xmlWriter->writeAttribute('text:style-name', $paragraphStyle);
            }
        }

        $this->replaceTabs($element->getBaseTextRun()->getText(), $xmlWriter);
        $this->writeText(' (');
        $this->replaceTabs($element->getRubyTextRun()->getText(), $xmlWriter);
        $this->writeText(')');

        if (!$this->withoutP) {
            $xmlWriter->endElement(); // text:p
        }
    }
}
