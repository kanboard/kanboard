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

namespace PhpOffice\PhpWord\Writer\HTML\Style;

use PhpOffice\PhpWord\Style\Font as FontStyle;

/**
 * Font style HTML writer.
 *
 * @since 0.10.0
 */
class Font extends AbstractStyle
{
    /**
     * Write style.
     *
     * @return string
     */
    public function write()
    {
        $style = $this->getStyle();
        if (!$style instanceof FontStyle) {
            return '';
        }
        $css = [];

        $font = $this->getFontFamily($style->getName(), $style->getFallbackFont());
        $size = $style->getSize();
        $color = $style->getColor();
        $fgColor = $style->getFgColor();
        $underline = $style->getUnderline() != FontStyle::UNDERLINE_NONE;
        $lineThrough = $style->isStrikethrough() || $style->isDoubleStrikethrough();

        $css['font-family'] = $this->getValueIf(!empty($font), $font);
        $css['font-size'] = $this->getValueIf($size !== null, "{$size}pt");
        $css['color'] = $this->getValueIf($color !== null, "#{$color}");
        $css['background'] = $this->getValueIf($fgColor != '', $fgColor);
        $css['font-weight'] = $this->getValueIf($style->isBold(), 'bold');
        $css['font-style'] = $this->getValueIf($style->isItalic(), 'italic');
        $css['vertical-align'] = '';
        $css['vertical-align'] .= $this->getValueIf($style->isSuperScript(), 'super');
        $css['vertical-align'] .= $this->getValueIf($style->isSubScript(), 'sub');
        $css['text-decoration'] = '';
        $css['text-decoration'] .= $this->getValueIf($underline, 'underline ');
        $css['text-decoration'] .= $this->getValueIf($lineThrough, 'line-through ');
        $css['text-transform'] = $this->getValueIf($style->isAllCaps(), 'uppercase');
        $css['font-variant'] = $this->getValueIf($style->isSmallCaps(), 'small-caps');
        $css['display'] = $this->getValueIf($style->isHidden(), 'none');
        $whitespace = $style->getWhiteSpace();
        if ($whitespace) {
            $css['white-space'] = $whitespace;
        }

        $spacing = $style->getSpacing();
        $css['letter-spacing'] = $this->getValueIf(null !== $spacing, ($spacing / 20) . 'pt');
        if ($style->isRTL()) {
            $css['direction'] = 'rtl';
        } elseif ($style->isRTL() === false) {
            $css['direction'] = 'ltr';
        }

        return $this->assembleCss($css);
    }

    /**
     * Set font and alternates for css font-family.
     */
    private function getFontFamily(?string $font, string $genericFont): string
    {
        if (empty($font)) {
            return '';
        }
        $fontfamily = "'" . htmlspecialchars($font, ENT_QUOTES, 'UTF-8') . "'";
        if (!empty($genericFont)) {
            $fontfamily .= ", $genericFont";
        }

        return $fontfamily;
    }
}
