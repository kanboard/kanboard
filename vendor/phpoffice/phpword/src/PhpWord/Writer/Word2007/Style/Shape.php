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
 * Shape style writer.
 *
 * @since 0.12.0
 */
class Shape extends AbstractStyle
{
    /**
     * Write style.
     */
    public function write(): void
    {
        $style = $this->getStyle();
        if (!$style instanceof \PhpOffice\PhpWord\Style\Shape) {
            return;
        }

        $xmlWriter = $this->getXmlWriter();

        $childStyles = ['Frame', 'Fill', 'Outline', 'Shadow', 'Extrusion'];
        foreach ($childStyles as $childStyle) {
            $method = "get{$childStyle}";
            $this->writeChildStyle($xmlWriter, $childStyle, $style->$method());
        }
    }
}
