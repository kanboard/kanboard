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

namespace PhpOffice\PhpWord\Writer\RTF\Style;

use PhpOffice\PhpWord\Style\Section as SectionStyle;

/**
 * RTF section style writer.
 *
 * @since 0.12.0
 */
class Section extends AbstractStyle
{
    /**
     * Write style.
     *
     * @return string
     */
    public function write()
    {
        $style = $this->getStyle();
        if (!$style instanceof SectionStyle) {
            return '';
        }

        $content = '';

        $content .= '\sectd ';

        // Size & margin
        $content .= $this->getValueIf($style->getPageSizeW() !== null, '\pgwsxn' . round($style->getPageSizeW()));
        $content .= $this->getValueIf($style->getPageSizeH() !== null, '\pghsxn' . round($style->getPageSizeH()));
        $content .= ' ';
        $content .= $this->getValueIf($style->getMarginTop() !== null, '\margtsxn' . round($style->getMarginTop()));
        $content .= $this->getValueIf($style->getMarginRight() !== null, '\margrsxn' . round($style->getMarginRight()));
        $content .= $this->getValueIf($style->getMarginBottom() !== null, '\margbsxn' . round($style->getMarginBottom()));
        $content .= $this->getValueIf($style->getMarginLeft() !== null, '\marglsxn' . round($style->getMarginLeft()));
        $content .= $this->getValueIf($style->getHeaderHeight() !== null, '\headery' . round($style->getHeaderHeight()));
        $content .= $this->getValueIf($style->getFooterHeight() !== null, '\footery' . round($style->getFooterHeight()));
        $content .= $this->getValueIf($style->getGutter() !== null, '\guttersxn' . round($style->getGutter()));
        $content .= $this->getValueIf($style->getPageNumberingStart() !== null, '\pgnstarts' . $style->getPageNumberingStart() . '\pgnrestart');
        $content .= ' ';

        // Borders
        if ($style->hasBorder()) {
            $styleWriter = new Border($style);
            $styleWriter->setParentWriter($this->getParentWriter());
            $styleWriter->setSizes($style->getBorderSize());
            $styleWriter->setColors($style->getBorderColor());
            $content .= $styleWriter->write();
        }

        return $content . PHP_EOL;
    }
}
