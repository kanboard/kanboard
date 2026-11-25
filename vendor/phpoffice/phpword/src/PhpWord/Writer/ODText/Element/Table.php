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

use PhpOffice\PhpWord\Element\Row as RowElement;
use PhpOffice\PhpWord\Element\Table as TableElement;
use PhpOffice\PhpWord\Shared\XMLWriter;

/**
 * Table element writer.
 *
 * @since 0.10.0
 */
class Table extends AbstractElement
{
    /**
     * Write element.
     */
    public function write(): void
    {
        $xmlWriter = $this->getXmlWriter();
        $element = $this->getElement();
        if (!$element instanceof TableElement) {
            return;
        }
        $rows = $element->getRows();
        $rowCount = count($rows);

        if ($rowCount > 0) {
            $xmlWriter->startElement('table:table');
            $xmlWriter->writeAttribute('table:name', $element->getElementId());
            $xmlWriter->writeAttribute('table:style-name', $element->getElementId());

            // Write columns
            $this->writeColumns($xmlWriter, $element);

            // Write rows
            foreach ($rows as $row) {
                $this->writeRow($xmlWriter, $row);
            }
            $xmlWriter->endElement(); // table:table
        }
    }

    /**
     * Write column.
     */
    private function writeColumns(XMLWriter $xmlWriter, TableElement $element): void
    {
        $colCount = $element->countColumns();

        for ($i = 0; $i < $colCount; ++$i) {
            $xmlWriter->startElement('table:table-column');
            $xmlWriter->writeAttribute('table:style-name', $element->getElementId() . '.' . $i);
            $xmlWriter->endElement();
        }
    }

    /**
     * Write row.
     */
    private function writeRow(XMLWriter $xmlWriter, RowElement $row): void
    {
        $xmlWriter->startElement('table:table-row');
        /** @var RowElement $row Type hint */
        foreach ($row->getCells() as $cell) {
            $xmlWriter->startElement('table:table-cell');
            $xmlWriter->writeAttribute('office:value-type', 'string');

            $containerWriter = new Container($xmlWriter, $cell);
            $containerWriter->write();

            $xmlWriter->endElement(); // table:table-cell
        }
        $xmlWriter->endElement(); // table:table-row
    }
}
