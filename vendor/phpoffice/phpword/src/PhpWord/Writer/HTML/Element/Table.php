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

namespace PhpOffice\PhpWord\Writer\HTML\Element;

use PhpOffice\PhpWord\Writer\HTML\Style\Table as TableStyleWriter;

/**
 * Table element HTML writer.
 *
 * @since 0.10.0
 */
class Table extends AbstractElement
{
    /**
     * Write table.
     *
     * @return string
     */
    public function write()
    {
        if (!$this->element instanceof \PhpOffice\PhpWord\Element\Table) {
            return '';
        }

        $content = '';
        $rows = $this->element->getRows();
        $rowCount = count($rows);
        if ($rowCount > 0) {
            $content .= '<table' . $this->getTableStyle($this->element->getStyle()) . '>' . PHP_EOL;

            for ($i = 0; $i < $rowCount; ++$i) {
                /** @var \PhpOffice\PhpWord\Element\Row $row Type hint */
                $rowStyle = $rows[$i]->getStyle();
                // $height = $row->getHeight();
                $tblHeader = $rowStyle->isTblHeader();
                $content .= '<tr>' . PHP_EOL;
                $rowCells = $rows[$i]->getCells();
                $rowCellCount = count($rowCells);
                for ($j = 0; $j < $rowCellCount; ++$j) {
                    $cellStyle = $rowCells[$j]->getStyle();
                    $cellStyleCss = $this->getTableStyle($cellStyle);
                    $cellBgColor = $cellStyle->getBgColor();
                    $cellFgColor = null;
                    if ($cellBgColor && $cellBgColor !== 'auto') {
                        $red = hexdec(substr($cellBgColor, 0, 2));
                        $green = hexdec(substr($cellBgColor, 2, 2));
                        $blue = hexdec(substr($cellBgColor, 4, 2));
                        $cellFgColor = (($red * 0.299 + $green * 0.587 + $blue * 0.114) > 186) ? null : 'ffffff';
                    }
                    $cellColSpan = $cellStyle->getGridSpan();
                    $cellRowSpan = 1;
                    $cellVMerge = $cellStyle->getVMerge();
                    // If this is the first cell of the vertical merge, find out how many rows it spans
                    if ($cellVMerge === 'restart') {
                        $cellRowSpan = $this->calculateCellRowSpan($rows, $i, $j);
                    }
                    // Ignore cells that are merged vertically with previous rows
                    if ($cellVMerge !== 'continue') {
                        $cellTag = $tblHeader ? 'th' : 'td';
                        $cellColSpanAttr = (is_numeric($cellColSpan) && ($cellColSpan > 1) ? " colspan=\"{$cellColSpan}\"" : '');
                        $cellRowSpanAttr = ($cellRowSpan > 1 ? " rowspan=\"{$cellRowSpan}\"" : '');
                        $cellBgColorAttr = (empty($cellBgColor) ? '' : " bgcolor=\"#{$cellBgColor}\"");
                        $cellFgColorAttr = (empty($cellFgColor) ? '' : " color=\"#{$cellFgColor}\"");
                        $content .= "<{$cellTag}{$cellStyleCss}{$cellColSpanAttr}{$cellRowSpanAttr}{$cellBgColorAttr}{$cellFgColorAttr}>" . PHP_EOL;
                        $writer = new Container($this->parentWriter, $rowCells[$j]);
                        $content .= $writer->write();
                        if ($cellRowSpan > 1) {
                            // There shouldn't be any content in the subsequent merged cells, but lets check anyway
                            for ($k = $i + 1; $k < $rowCount; ++$k) {
                                $kRowCells = $rows[$k]->getCells();
                                if (isset($kRowCells[$j]) && $kRowCells[$j]->getStyle()->getVMerge() === 'continue') {
                                    $writer = new Container($this->parentWriter, $kRowCells[$j]);
                                    $content .= $writer->write();
                                } else {
                                    break;
                                }
                            }
                        }
                        $content .= "</{$cellTag}>" . PHP_EOL;
                    }
                }
                $content .= '</tr>' . PHP_EOL;
            }
            $content .= '</table>' . PHP_EOL;
        }

        return $content;
    }

    /**
     * Translates Table style in CSS equivalent.
     *
     * @param null|\PhpOffice\PhpWord\Style\Cell|\PhpOffice\PhpWord\Style\Table|string $tableStyle
     */
    private function getTableStyle($tableStyle = null): string
    {
        if ($tableStyle == null) {
            return '';
        }
        if (is_string($tableStyle)) {
            return ' class="' . $tableStyle . '"';
        }

        $styleWriter = new TableStyleWriter($tableStyle);
        $style = $styleWriter->write();
        if ($style === '') {
            return '';
        }

        return ' style="' . $style . '"';
    }

    /**
     * Calculates cell rowspan.
     *
     * @param \PhpOffice\PhpWord\Element\Row[] $rows
     */
    private function calculateCellRowSpan(array $rows, int $rowIndex, int $colIndex): int
    {
        $currentRow = $rows[$rowIndex];
        $currentRowCells = $currentRow->getCells();
        $shiftedColIndex = 0;

        foreach ($currentRowCells as $cell) {
            if ($cell === $currentRowCells[$colIndex]) {
                break;
            }

            $colSpan = 1;

            if ($cell->getStyle()->getGridSpan() !== null) {
                $colSpan = $cell->getStyle()->getGridSpan();
            }

            $shiftedColIndex += $colSpan;
        }

        $rowCount = count($rows);
        $rowSpan = 1;

        for ($i = $rowIndex + 1; $i < $rowCount; ++$i) {
            $rowCells = $rows[$i]->getCells();
            $colIndex = 0;

            foreach ($rowCells as $cell) {
                if ($colIndex === $shiftedColIndex) {
                    if ($cell->getStyle()->getVMerge() === 'continue') {
                        ++$rowSpan;
                    }

                    break;
                }

                $colSpan = 1;

                if ($cell->getStyle()->getGridSpan() !== null) {
                    $colSpan = $cell->getStyle()->getGridSpan();
                }

                $colIndex += $colSpan;
            }
        }

        return $rowSpan;
    }
}
