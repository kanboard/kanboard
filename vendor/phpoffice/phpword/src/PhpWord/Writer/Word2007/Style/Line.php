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

use PhpOffice\PhpWord\Style\Line as LineStyle;

/**
 * Line style writer.
 */
class Line extends Frame
{
    /**
     * Write Line stroke.
     *
     * @todo Merge with `Stroke` style
     */
    public function writeStroke(): void
    {
        $xmlWriter = $this->getXmlWriter();
        $style = $this->getStyle();
        if (!$style instanceof LineStyle) {
            return;
        }

        $dash = $style->getDash();
        $dashStyles = [
            LineStyle::DASH_STYLE_DASH => 'dash',
            LineStyle::DASH_STYLE_ROUND_DOT => '1 1',
            LineStyle::DASH_STYLE_SQUARE_DOT => '1 1',
            LineStyle::DASH_STYLE_DASH_DOT => 'dashDot',
            LineStyle::DASH_STYLE_LONG_DASH => 'longDash',
            LineStyle::DASH_STYLE_LONG_DASH_DOT => 'longDashDot',
            LineStyle::DASH_STYLE_LONG_DASH_DOT_DOT => 'longDashDotDot',
        ];

        $xmlWriter->startElement('v:stroke');

        $xmlWriter->writeAttributeIf($style->getWeight() !== null, 'weight', $style->getWeight() . 'pt');
        $xmlWriter->writeAttributeIf($style->getColor() !== null, 'color', $style->getColor());
        $xmlWriter->writeAttributeIf($style->getBeginArrow() !== null, 'startarrow', $style->getBeginArrow());
        $xmlWriter->writeAttributeIf($style->getEndArrow() !== null, 'endarrow', $style->getEndArrow());

        if ($dash !== null) {
            if (isset($dashStyles[$dash])) {
                $xmlWriter->writeAttribute('dashstyle', $dashStyles[$dash]);
            }
            if ($dash == LineStyle::DASH_STYLE_ROUND_DOT) {
                $xmlWriter->writeAttribute('endcap', 'round');
            }
        }

        $xmlWriter->endElement(); //v:stroke
    }
}
