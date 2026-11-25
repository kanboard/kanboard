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

namespace PhpOffice\PhpWord\Writer\RTF\Element;

use PhpOffice\PhpWord\Element\Cell as CellElement;
use PhpOffice\PhpWord\Element\Row as RowElement;
use PhpOffice\PhpWord\Element\Table as TableElement;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\SimpleType\Border;
use PhpOffice\PhpWord\Style;
use PhpOffice\PhpWord\Style\Cell as CellStyle;
use PhpOffice\PhpWord\Style\Table as TableStyle;

/**
 * Table element RTF writer.
 *
 * @since 0.11.0
 */
class Table extends AbstractElement
{
    /**
     * @var TableElement
     */
    protected $element;

    /**
     * Write element.
     *
     * @return string
     */
    public function write()
    {
        if (!$this->element instanceof TableElement) {
            return '';
        }
        $element = $this->element;
        // No nesting table for now
        if ($element->getNestedLevel() >= 1) {
            return '';
        }

        $content = '';
        $style = $this->element->getStyle();
        $bidiStyle = (is_object($style) && method_exists($style, 'isBidiVisual')) ? $style->isBidiVisual() : Settings::isDefaultRtl();
        $bidi = $bidiStyle ? '\rtlrow' : '';
        $rows = $element->getRows();
        $rowCount = count($rows);

        if ($rowCount > 0) {
            $content .= '\pard' . PHP_EOL;

            for ($i = 0; $i < $rowCount; ++$i) {
                $content .= "\\trowd$bidi ";
                $content .= $this->writeRowDef($rows[$i]);
                $content .= PHP_EOL;
                $content .= $this->writeRow($rows[$i]);
                $content .= '\row' . PHP_EOL;
            }
            $content .= '\pard' . PHP_EOL;
        }

        return $content;
    }

    /**
     * Write column.
     *
     * @return string
     */
    private function writeRowDef(RowElement $row)
    {
        $content = '';
        $tableStyle = $this->element->getStyle();
        if (is_string($tableStyle)) {
            $tableStyle = Style::getStyle($tableStyle);
            if (!($tableStyle instanceof TableStyle)) {
                $tableStyle = null;
            }
        }

        $rightMargin = 0;
        foreach ($row->getCells() as $cell) {
            $content .= $this->writeCellStyle($cell->getStyle(), $tableStyle);

            $width = $cell->getWidth();
            $vMerge = $this->getVMerge($cell->getStyle()->getVMerge());
            if ($width === null) {
                $width = 720; // Arbitrary default width
            }
            $rightMargin += $width;
            $content .= "{$vMerge}\\cellx{$rightMargin} ";
        }

        return $content;
    }

    /**
     * Write row.
     *
     * @return string
     */
    private function writeRow(RowElement $row)
    {
        $content = '';

        // Write cells
        foreach ($row->getCells() as $cell) {
            $content .= $this->writeCell($cell);
        }

        return $content;
    }

    /**
     * Write cell.
     *
     * @return string
     */
    private function writeCell(CellElement $cell)
    {
        $content = '\intbl' . PHP_EOL;

        // Write content
        $writer = new Container($this->parentWriter, $cell);
        $content .= $writer->write();

        $content .= '\cell' . PHP_EOL;

        return $content;
    }

    private function writeCellStyle(CellStyle $cell, ?TableStyle $table): string
    {
        $content = $this->writeCellBorder(
            't',
            $cell->getBorderTopStyle() ?: ($table ? $table->getBorderTopStyle() : null),
            (int) round($cell->getBorderTopSize() ?: ($table ? ($table->getBorderTopSize() ?: 0) : 0)),
            $cell->getBorderTopColor() ?? ($table ? $table->getBorderTopColor() : null)
        );
        $content .= $this->writeCellBorder(
            'l',
            $cell->getBorderLeftStyle() ?: ($table ? $table->getBorderLeftStyle() : null),
            (int) round($cell->getBorderLeftSize() ?: ($table ? ($table->getBorderLeftSize() ?: 0) : 0)),
            $cell->getBorderLeftColor() ?? ($table ? $table->getBorderLeftColor() : null)
        );
        $content .= $this->writeCellBorder(
            'b',
            $cell->getBorderBottomStyle() ?: ($table ? $table->getBorderBottomStyle() : null),
            (int) round($cell->getBorderBottomSize() ?: ($table ? ($table->getBorderBottomSize() ?: 0) : 0)),
            $cell->getBorderBottomColor() ?? ($table ? $table->getBorderBottomColor() : null)
        );
        $content .= $this->writeCellBorder(
            'r',
            $cell->getBorderRightStyle() ?: ($table ? $table->getBorderRightStyle() : null),
            (int) round($cell->getBorderRightSize() ?: ($table ? ($table->getBorderRightSize() ?: 0) : 0)),
            $cell->getBorderRightColor() ?? ($table ? $table->getBorderRightColor() : null)
        );

        return $content;
    }

    private function writeCellBorder(string $prefix, ?string $borderStyle, int $borderSize, ?string $borderColor): string
    {
        if ($borderSize == 0) {
            return '';
        }

        $content = '\clbrdr' . $prefix;
        /**
         * \brdrs 	Single-thickness border.
         * \brdrth 	Double-thickness border.
         * \brdrsh 	Shadowed border.
         * \brdrdb 	Double border.
         * \brdrdot 	Dotted border.
         * \brdrdash 	Dashed border.
         * \brdrhair 	Hairline border.
         * \brdrinset 	Inset border.
         * \brdrdashsm 	Dash border (small).
         * \brdrdashd 	Dot dash border.
         * \brdrdashdd 	Dot dot dash border.
         * \brdroutset 	Outset border.
         * \brdrtriple 	Triple border.
         * \brdrtnthsg 	Thick thin border (small).
         * \brdrthtnsg 	Thin thick border (small).
         * \brdrtnthtnsg 	Thin thick thin border (small).
         * \brdrtnthmg 	Thick thin border (medium).
         * \brdrthtnmg 	Thin thick border (medium).
         * \brdrtnthtnmg 	Thin thick thin border (medium).
         * \brdrtnthlg 	Thick thin border (large).
         * \brdrthtnlg 	Thin thick border (large).
         * \brdrtnthtnlg 	Thin thick thin border (large).
         * \brdrwavy 	Wavy border.
         * \brdrwavydb 	Double wavy border.
         * \brdrdashdotstr 	Striped border.
         * \brdremboss 	Emboss border.
         * \brdrengrave 	Engrave border.
         */
        switch ($borderStyle) {
            case Border::DOTTED:
                $content .= '\brdrdot';

                break;
            case Border::SINGLE:
            default:
                $content .= '\brdrs';

                break;
        }

        // \brdrwN 	N is the width in twips (1/20 pt) of the pen used to draw the paragraph border line.
        //          N cannot be greater than 75.
        //          To obtain a larger border width, the \brdth control word can be used to obtain a width double that of N.
        // $borderSize is in eights of a point, i.e. 4 / 8 = .5pt
        // 1/20 pt => 1/8 / 2.5
        $content .= '\brdrw' . (int) ($borderSize / 2.5);

        // \brdrcfN 	N is the color of the paragraph border, specified as an index into the color table in the RTF header.
        $colorIndex = 0;
        $index = array_search($borderColor, $this->parentWriter->getColorTable());
        if ($index !== false) {
            $colorIndex = (int) $index + 1;
        }
        $content .= '\brdrcf' . $colorIndex;
        $content .= PHP_EOL;

        return $content;
    }

    /**
     * Get vertical merge style.
     *
     * @param string $value
     *
     * @return string
     *
     * @todo Move to style
     */
    private function getVMerge($value)
    {
        $style = '';
        if ($value == 'restart') {
            $style = '\clvmgf';
        } elseif ($value == 'continue') {
            $style = '\clvmrg';
        }

        return $style;
    }
}
