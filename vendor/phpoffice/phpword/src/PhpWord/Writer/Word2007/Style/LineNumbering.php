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

namespace PhpOffice\PhpWord\Writer\Word2007\Style;

/**
 * Line numbering style writer.
 *
 * @since 0.10.0
 */
class LineNumbering extends AbstractStyle
{
    /**
     * Write style.
     * The w:start seems to be zero based so we have to decrement by one.
     */
    public function write(): void
    {
        $style = $this->getStyle();
        if (!$style instanceof \PhpOffice\PhpWord\Style\LineNumbering) {
            return;
        }
        $xmlWriter = $this->getXmlWriter();

        $xmlWriter->startElement('w:lnNumType');
        $xmlWriter->writeAttribute('w:start', $style->getStart() - 1);
        $xmlWriter->writeAttribute('w:countBy', $style->getIncrement());
        $xmlWriter->writeAttribute('w:distance', $style->getDistance());
        $xmlWriter->writeAttribute('w:restart', $style->getRestart());
        $xmlWriter->endElement();
    }
}
