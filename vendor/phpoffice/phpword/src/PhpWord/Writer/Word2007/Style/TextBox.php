<?php

/**
 * This file is part of PHPWord - A pure PHP library for reading and writing
 * word processing documents.
 * PHPWord is free software distributed under the terms of the GNU Lesser
 * General Public License version 3 as published by the Free Software Foundation.
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code. For the full list of
 * contributors, visit https://github.com/PHPOffice/PHPWord/contributors.
 *
 * @see         https://github.com/PHPOffice/PHPWord
 *
 * @license     http://www.gnu.org/licenses/lgpl.txt LGPL version 3
 */

namespace PhpOffice\PhpWord\Writer\Word2007\Style;

use PhpOffice\PhpWord\Style\TextBox as TextBoxStyle;

/**
 * TextBox style writer.
 *
 * @since 0.11.0
 */
class TextBox extends Frame
{
    /**
     * Writer inner margin.
     */
    public function writeInnerMargin(): void
    {
        $style = $this->getStyle();
        if (!$style instanceof TextBoxStyle || !$style->hasInnerMargins()) {
            return;
        }

        $xmlWriter = $this->getXmlWriter();
        $margins = implode(', ', $style->getInnerMargin());

        $xmlWriter->writeAttribute('inset', $margins);
    }

    /**
     * Writer border.
     */
    public function writeBorder(): void
    {
        $style = $this->getStyle();
        if (!$style instanceof TextBoxStyle) {
            return;
        }
        $xmlWriter = $this->getXmlWriter();

        $xmlWriter->startElement('v:stroke');
        $xmlWriter->writeAttributeIf($style->getBorderSize() !== null, 'weight', $style->getBorderSize() . 'pt');
        $xmlWriter->writeAttributeIf($style->getBorderColor() !== null, 'color', $style->getBorderColor());
        $xmlWriter->endElement(); // v:stroke
    }
}
