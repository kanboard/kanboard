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
 * Font style writer.
 *
 * @since 0.10.0
 */
class Font extends AbstractStyle
{
    /**
     * Write style.
     */
    public function write(): void
    {
        $style = $this->getStyle();
        if (!$style instanceof \PhpOffice\PhpWord\Style\Font) {
            return;
        }
        $xmlWriter = $this->getXmlWriter();

        $stylep = (method_exists($style, 'getParagraph')) ? $style->getParagraph() : null;
        if ($stylep instanceof \PhpOffice\PhpWord\Style\Paragraph) {
            $temp1 = clone $stylep;
            $temp1->setStyleName($style->getStyleName());
            $temp2 = new Paragraph($xmlWriter, $temp1);
            $temp2->write();
        }

        $xmlWriter->startElement('style:style');
        $xmlWriter->writeAttribute('style:name', $style->getStyleName());
        $xmlWriter->writeAttribute('style:family', 'text');
        $xmlWriter->startElement('style:text-properties');

        // Name
        $font = $style->getName();
        $xmlWriter->writeAttributeIf($font != '', 'style:font-name', $font);
        $xmlWriter->writeAttributeIf($font != '', 'style:font-name-complex', $font);
        $size = $style->getSize();

        // Size
        $xmlWriter->writeAttributeIf(is_numeric($size), 'fo:font-size', $size . 'pt');
        $xmlWriter->writeAttributeIf(is_numeric($size), 'style:font-size-asian', $size . 'pt');
        $xmlWriter->writeAttributeIf(is_numeric($size), 'style:font-size-complex', $size . 'pt');

        // Color
        $color = $style->getColor();
        $xmlWriter->writeAttributeIf($color != '', 'fo:color', '#' . \PhpOffice\PhpWord\Shared\Converter::stringToRgb($color));

        // Bold & italic
        $xmlWriter->writeAttributeIf($style->isBold(), 'fo:font-weight', 'bold');
        $xmlWriter->writeAttributeIf($style->isBold(), 'style:font-weight-asian', 'bold');
        $xmlWriter->writeAttributeIf($style->isItalic(), 'fo:font-style', 'italic');
        $xmlWriter->writeAttributeIf($style->isItalic(), 'style:font-style-asian', 'italic');
        $xmlWriter->writeAttributeIf($style->isItalic(), 'style:font-style-complex', 'italic');

        // Underline
        // @todo Various mode of underline
        $underline = $style->getUnderline();
        $xmlWriter->writeAttributeIf($underline != 'none', 'style:text-underline-style', 'solid');

        // Strikethrough, double strikethrough
        $xmlWriter->writeAttributeIf($style->isStrikethrough(), 'style:text-line-through-type', 'single');
        $xmlWriter->writeAttributeIf($style->isDoubleStrikethrough(), 'style:text-line-through-type', 'double');

        // Small caps, all caps
        $xmlWriter->writeAttributeIf($style->isSmallCaps(), 'fo:font-variant', 'small-caps');
        $xmlWriter->writeAttributeIf($style->isAllCaps(), 'fo:text-transform', 'uppercase');

        //Hidden text
        $xmlWriter->writeAttributeIf($style->isHidden(), 'text:display', 'none');

        // Superscript/subscript
        $xmlWriter->writeAttributeIf($style->isSuperScript(), 'style:text-position', 'super');
        $xmlWriter->writeAttributeIf($style->isSubScript(), 'style:text-position', 'sub');

        if ($style->isNoProof()) {
            $xmlWriter->writeAttribute('fo:language', 'zxx');
            $xmlWriter->writeAttribute('style:language-asian', 'zxx');
            $xmlWriter->writeAttribute('style:language-complex', 'zxx');
            $xmlWriter->writeAttribute('fo:country', 'none');
            $xmlWriter->writeAttribute('style:country-asian', 'none');
            $xmlWriter->writeAttribute('style:country-complex', 'none');
        }

        // @todo Foreground-Color

        // @todo Background color

        $xmlWriter->endElement(); // style:text-properties
        $xmlWriter->endElement(); // style:style
    }
}
