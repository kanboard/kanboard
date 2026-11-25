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
 * Font style writer.
 *
 * @since 0.10.0
 */
class Font extends AbstractStyle
{
    /**
     * Is inline in element.
     *
     * @var bool
     */
    private $isInline = false;

    /**
     * Write style.
     */
    public function write(): void
    {
        $xmlWriter = $this->getXmlWriter();

        $isStyleName = $this->isInline && null !== $this->style && is_string($this->style);
        if ($isStyleName) {
            $xmlWriter->startElement('w:rPr');
            $xmlWriter->startElement('w:rStyle');
            $xmlWriter->writeAttribute('w:val', $this->style);
            $xmlWriter->endElement();
            $style = \PhpOffice\PhpWord\Style::getStyle($this->style);
            if ($style instanceof \PhpOffice\PhpWord\Style\Font) {
                $xmlWriter->writeElementIf($style->isRTL(), 'w:rtl');
            }
            $xmlWriter->endElement();
        } else {
            $this->writeStyle();
        }
    }

    /**
     * Write full style.
     */
    private function writeStyle(): void
    {
        $style = $this->getStyle();
        if (!$style instanceof \PhpOffice\PhpWord\Style\Font) {
            return;
        }

        $xmlWriter = $this->getXmlWriter();

        $xmlWriter->startElement('w:rPr');

        // Style name
        if ($this->isInline === true) {
            $styleName = $style->getStyleName();
            $xmlWriter->writeElementIf($styleName !== null, 'w:rStyle', 'w:val', $styleName);
        }

        // Font name/family
        $font = $style->getName();
        $hint = $style->getHint();
        if ($font !== null) {
            $xmlWriter->startElement('w:rFonts');
            $xmlWriter->writeAttribute('w:ascii', $font);
            $xmlWriter->writeAttribute('w:hAnsi', $font);
            $xmlWriter->writeAttribute('w:eastAsia', $font);
            $xmlWriter->writeAttribute('w:cs', $font);
            $xmlWriter->writeAttributeIf($hint !== null, 'w:hint', $hint);
            $xmlWriter->endElement();
        }

        //Language
        $language = $style->getLang();
        if ($language != null && ($language->getLatin() !== null || $language->getEastAsia() !== null || $language->getBidirectional() !== null)) {
            $xmlWriter->startElement('w:lang');
            $xmlWriter->writeAttributeIf($language->getLatin() !== null, 'w:val', $language->getLatin());
            $xmlWriter->writeAttributeIf($language->getEastAsia() !== null, 'w:eastAsia', $language->getEastAsia());
            $xmlWriter->writeAttributeIf($language->getBidirectional() !== null, 'w:bidi', $language->getBidirectional());
            //if bidi is not set but we are writing RTL, write the latin language in the bidi tag
            if ($style->isRTL() && $language->getBidirectional() === null && $language->getLatin() !== null) {
                $xmlWriter->writeAttribute('w:bidi', $language->getLatin());
            }
            $xmlWriter->endElement();
        }

        // Color
        $color = $style->getColor();
        $xmlWriter->writeElementIf($color !== null, 'w:color', 'w:val', $color);

        // Size
        $size = $style->getSize();
        $xmlWriter->writeElementIf($size !== null, 'w:sz', 'w:val', $size * 2);
        $xmlWriter->writeElementIf($size !== null, 'w:szCs', 'w:val', $size * 2);

        // Bold, italic
        $xmlWriter->writeElementIf($style->isBold() !== null, 'w:b', 'w:val', $this->writeOnOf($style->isBold()));
        $xmlWriter->writeElementIf($style->isBold() !== null, 'w:bCs', 'w:val', $this->writeOnOf($style->isBold()));
        $xmlWriter->writeElementIf($style->isItalic() !== null, 'w:i', 'w:val', $this->writeOnOf($style->isItalic()));
        $xmlWriter->writeElementIf($style->isItalic() !== null, 'w:iCs', 'w:val', $this->writeOnOf($style->isItalic()));

        // Strikethrough, double strikethrough
        $xmlWriter->writeElementIf($style->isStrikethrough(), 'w:strike', 'w:val', $this->writeOnOf($style->isStrikethrough()));
        $xmlWriter->writeElementIf($style->isDoubleStrikethrough(), 'w:dstrike', 'w:val', $this->writeOnOf($style->isDoubleStrikethrough()));

        // Small caps, all caps
        $xmlWriter->writeElementIf($style->isSmallCaps() !== null, 'w:smallCaps', 'w:val', $this->writeOnOf($style->isSmallCaps()));
        $xmlWriter->writeElementIf($style->isAllCaps() !== null, 'w:caps', 'w:val', $this->writeOnOf($style->isAllCaps()));

        //Hidden text
        $xmlWriter->writeElementIf($style->isHidden(), 'w:vanish', 'w:val', $this->writeOnOf($style->isHidden()));

        // Underline
        $xmlWriter->writeElementIf($style->getUnderline() != 'none', 'w:u', 'w:val', $style->getUnderline());

        // Foreground-Color
        $xmlWriter->writeElementIf($style->getFgColor() !== null, 'w:highlight', 'w:val', $style->getFgColor());

        // Superscript/subscript
        $xmlWriter->writeElementIf($style->isSuperScript(), 'w:vertAlign', 'w:val', 'superscript');
        $xmlWriter->writeElementIf($style->isSubScript(), 'w:vertAlign', 'w:val', 'subscript');

        // Spacing
        $xmlWriter->writeElementIf($style->getScale() !== null, 'w:w', 'w:val', $style->getScale());
        $xmlWriter->writeElementIf($style->getSpacing() !== null, 'w:spacing', 'w:val', $style->getSpacing());
        $xmlWriter->writeElementIf($style->getKerning() !== null, 'w:kern', 'w:val', $style->getKerning() * 2);

        // noProof
        $xmlWriter->writeElementIf($style->isNoProof() !== null, 'w:noProof', 'w:val', $this->writeOnOf($style->isNoProof()));

        // Background-Color
        $shading = $style->getShading();
        if (null !== $shading) {
            $styleWriter = new Shading($xmlWriter, $shading);
            $styleWriter->write();
        }

        // RTL
        if ($this->isInline === true) {
            $styleName = $style->getStyleName();
            $xmlWriter->writeElementIf($styleName === null && $style->isRTL(), 'w:rtl');
        }

        // Position
        $xmlWriter->writeElementIf($style->getPosition() !== null, 'w:position', 'w:val', $style->getPosition());

        $xmlWriter->endElement();
    }

    /**
     * Set is inline.
     *
     * @param bool $value
     */
    public function setIsInline($value): void
    {
        $this->isInline = $value;
    }
}
