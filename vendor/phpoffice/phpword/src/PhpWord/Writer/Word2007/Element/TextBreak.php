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
 * TextBreak element writer.
 *
 * @since 0.10.0
 */
class TextBreak extends Text
{
    /**
     * Write text break element.
     */
    public function write(): void
    {
        $xmlWriter = $this->getXmlWriter();
        $element = $this->getElement();
        if (!$element instanceof \PhpOffice\PhpWord\Element\TextBreak) {
            return;
        }

        if (!$this->withoutP) {
            $hasStyle = $element->hasStyle();
            $this->startElementP();

            if ($hasStyle) {
                $xmlWriter->startElement('w:pPr');
                $this->writeFontStyle();
                $xmlWriter->endElement(); // w:pPr
            }

            $this->endElementP(); // w:p
        } else {
            $xmlWriter->writeElement('w:br');
        }
    }
}
