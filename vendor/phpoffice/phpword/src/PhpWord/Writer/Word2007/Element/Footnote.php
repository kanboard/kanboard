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
 * Footnote element writer.
 *
 * @since 0.10.0
 */
class Footnote extends Text
{
    /**
     * Reference type footnoteReference|endnoteReference.
     *
     * @var string
     */
    protected $referenceType = 'footnoteReference';

    /**
     * Write element.
     */
    public function write(): void
    {
        $xmlWriter = $this->getXmlWriter();
        $element = $this->getElement();
        if (!$element instanceof \PhpOffice\PhpWord\Element\Footnote) {
            return;
        }

        $this->startElementP();

        $xmlWriter->startElement('w:r');
        $xmlWriter->startElement('w:rPr');
        $xmlWriter->startElement('w:rStyle');
        $xmlWriter->writeAttribute('w:val', ucfirst($this->referenceType));
        $xmlWriter->endElement(); // w:rStyle
        $xmlWriter->endElement(); // w:rPr
        $xmlWriter->startElement("w:{$this->referenceType}");
        $xmlWriter->writeAttribute('w:id', $element->getRelationId() + 1);
        $xmlWriter->endElement(); // w:$referenceType
        $xmlWriter->endElement(); // w:r

        $this->endElementP(); // w:p
    }
}
