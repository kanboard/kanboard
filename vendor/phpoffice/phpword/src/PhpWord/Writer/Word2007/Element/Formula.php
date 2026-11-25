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

use PhpOffice\Math\Writer\OfficeMathML;
use PhpOffice\PhpWord\Element\Formula as FormulaElement;

/**
 * Formula element writer.
 */
class Formula extends AbstractElement
{
    /**
     * Write element.
     */
    public function write(): void
    {
        $element = $this->getElement();
        if (!$element instanceof FormulaElement) {
            return;
        }

        $this->startElementP();

        $xmlWriter = $this->getXmlWriter();

        $xmlWriter->startElement('w:r');
        $xmlWriter->writeElement('w:rPr');
        $xmlWriter->endElement();

        $xmlWriter->writeRaw((new OfficeMathML())->write($element->getMath()));

        $this->endElementP();
    }
}
