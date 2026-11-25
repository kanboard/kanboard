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
 * PageBreak element writer.
 *
 * @since 0.10.0
 */
class PageBreak extends AbstractElement
{
    /**
     * Write element.
     *
     * @usedby \PhpOffice\PhpWord\Writer\Word2007\Element\AbstractElement::startElementP()
     */
    public function write(): void
    {
        $xmlWriter = $this->getXmlWriter();

        $xmlWriter->startElement('w:p');
        $xmlWriter->startElement('w:r');
        $xmlWriter->startElement('w:br');
        $xmlWriter->writeAttribute('w:type', 'page');
        $xmlWriter->endElement(); // w:br
        $xmlWriter->endElement(); // w:r
        $xmlWriter->endElement(); // w:p
    }
}
