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

use PhpOffice\PhpWord\Style\Cell as CellStyle;

/**
 * Cell style writer.
 *
 * @since 0.10.0
 */
class Cell extends AbstractStyle
{
    /**
     * @var int Cell width
     */
    private $width;

    /**
     * Write style.
     */
    public function write(): void
    {
        $style = $this->getStyle();
        if (!$style instanceof CellStyle) {
            return;
        }
        $xmlWriter = $this->getXmlWriter();

        $xmlWriter->startElement('w:tcPr');

        // Width
        if (null !== $this->width || null !== $style->getWidth()) {
            $width = null === $this->width ? $style->getWidth() : $this->width;

            $xmlWriter->startElement('w:tcW');
            $xmlWriter->writeAttribute('w:w', $width);
            $xmlWriter->writeAttribute('w:type', $style->getUnit());
            $xmlWriter->endElement(); // w:tcW
        }

        $paddingTop = $style->getPaddingTop();
        $paddingLeft = $style->getPaddingLeft();
        $paddingBottom = $style->getPaddingBottom();
        $paddingRight = $style->getPaddingRight();

        if ($paddingTop !== null || $paddingLeft !== null || $paddingBottom !== null || $paddingRight !== null) {
            $xmlWriter->startElement('w:tcMar');
            if ($paddingTop !== null) {
                $xmlWriter->startElement('w:top');
                $xmlWriter->writeAttribute('w:w', $paddingTop);
                $xmlWriter->writeAttribute('w:type', \PhpOffice\PhpWord\SimpleType\TblWidth::TWIP);
                $xmlWriter->endElement(); // w:top
            }
            if ($paddingLeft !== null) {
                $xmlWriter->startElement('w:start');
                $xmlWriter->writeAttribute('w:w', $paddingLeft);
                $xmlWriter->writeAttribute('w:type', \PhpOffice\PhpWord\SimpleType\TblWidth::TWIP);
                $xmlWriter->endElement(); // w:start
            }
            if ($paddingBottom !== null) {
                $xmlWriter->startElement('w:bottom');
                $xmlWriter->writeAttribute('w:w', $paddingBottom);
                $xmlWriter->writeAttribute('w:type', \PhpOffice\PhpWord\SimpleType\TblWidth::TWIP);
                $xmlWriter->endElement(); // w:bottom
            }
            if ($paddingRight !== null) {
                $xmlWriter->startElement('w:end');
                $xmlWriter->writeAttribute('w:w', $paddingRight);
                $xmlWriter->writeAttribute('w:type', \PhpOffice\PhpWord\SimpleType\TblWidth::TWIP);
                $xmlWriter->endElement(); // w:end
            }
            $xmlWriter->endElement(); // w:tcMar
        }

        // Text direction
        $textDir = $style->getTextDirection();
        $xmlWriter->writeElementIf(null !== $textDir, 'w:textDirection', 'w:val', $textDir);

        // Vertical alignment
        $vAlign = $style->getVAlign();
        $xmlWriter->writeElementIf(null !== $vAlign, 'w:vAlign', 'w:val', $vAlign);

        // Border
        if ($style->hasBorder()) {
            $xmlWriter->startElement('w:tcBorders');

            $styleWriter = new MarginBorder($xmlWriter);
            $styleWriter->setSizes($style->getBorderSize());
            $styleWriter->setColors($style->getBorderColor());
            $styleWriter->setStyles($style->getBorderStyle());
            $styleWriter->setAttributes(['defaultColor' => CellStyle::DEFAULT_BORDER_COLOR]);
            $styleWriter->write();

            $xmlWriter->endElement();
        }

        // Shading
        $shading = $style->getShading();
        if (null !== $shading) {
            $styleWriter = new Shading($xmlWriter, $shading);
            $styleWriter->write();
        }

        // Colspan & rowspan
        $gridSpan = $style->getGridSpan();
        $vMerge = $style->getVMerge();
        $xmlWriter->writeElementIf(null !== $gridSpan, 'w:gridSpan', 'w:val', $gridSpan);
        $xmlWriter->writeElementIf(null !== $vMerge, 'w:vMerge', 'w:val', $vMerge);
        $xmlWriter->writeElementIf($style->getNoWrap(), 'w:noWrap');

        $xmlWriter->endElement(); // w:tcPr
    }

    /**
     * Set width.
     *
     * @param int $value
     */
    public function setWidth($value = null): void
    {
        $this->width = $value;
    }
}
