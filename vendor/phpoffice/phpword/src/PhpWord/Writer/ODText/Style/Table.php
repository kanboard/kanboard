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

namespace PhpOffice\PhpWord\Writer\ODText\Style;

/**
 * Table style writer.
 *
 * @since 0.11.0
 */
class Table extends AbstractStyle
{
    /**
     * Write style.
     */
    public function write(): void
    {
        /** @var \PhpOffice\PhpWord\Style\Table $style Type hint */
        $style = $this->getStyle();
        if (!$style instanceof \PhpOffice\PhpWord\Style\Table) {
            return;
        }
        $xmlWriter = $this->getXmlWriter();

        $xmlWriter->startElement('style:style');
        $xmlWriter->writeAttribute('style:name', $style->getStyleName());
        $xmlWriter->writeAttribute('style:family', 'table');
        $xmlWriter->startElement('style:table-properties');
        //$xmlWriter->writeAttribute('style:width', 'table');
        $xmlWriter->writeAttribute('style:rel-width', 100);
        $xmlWriter->writeAttribute('table:align', 'center');
        $xmlWriter->writeAttributeIf($style->isBidiVisual(), 'style:writing-mode', 'rl-tb');
        $xmlWriter->endElement(); // style:table-properties
        $xmlWriter->endElement(); // style:style

        $cellWidths = $style->getColumnWidths();
        $countCellWidths = $cellWidths === null ? 0 : count($cellWidths);

        for ($i = 0; $i < $countCellWidths; ++$i) {
            $width = $cellWidths[$i];
            $xmlWriter->startElement('style:style');
            $xmlWriter->writeAttribute('style:name', $style->getStyleName() . '.' . $i);
            $xmlWriter->writeAttribute('style:family', 'table-column');
            $xmlWriter->startElement('style:table-column-properties');
            $xmlWriter->writeAttribute('style:column-width', number_format($width * 0.0017638889, 2, '.', '') . 'cm');
            $xmlWriter->endElement(); // style:table-column-properties
            $xmlWriter->endElement(); // style:style
        }
    }
}
