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

/**
 * Paragraph style HTML writer.
 *
 * @since 0.10.0
 */
class Image extends AbstractStyle
{
    /**
     * Write style.
     *
     * @return string
     */
    public function write()
    {
        $style = $this->getStyle();
        if (!$style instanceof \PhpOffice\PhpWord\Style\Image) {
            return '';
        }
        $css = [];

        $width = $style->getWidth();
        $height = $style->getHeight();
        $css['width'] = $this->getValueIf(is_numeric($width), $width . 'px');
        $css['height'] = $this->getValueIf(is_numeric($height), $height . 'px');

        return $this->assembleCss($css);
    }
}
