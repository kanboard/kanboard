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
declare(strict_types=1);

namespace PhpOffice\PhpWord\Writer\HTML\Style;

use PhpOffice\PhpWord\Style\Cell as StyleCell;
use PhpOffice\PhpWord\Style\Table as StyleTable;

class Table extends AbstractStyle
{
    /**
     * @return string
     */
    public function write()
    {
        $style = $this->getStyle();
        if (!$style instanceof StyleTable && !$style instanceof StyleCell) {
            return '';
        }

        $css = [];
        if (is_object($style) && method_exists($style, 'getLayout')) {
            if ($style->getLayout() == StyleTable::LAYOUT_FIXED) {
                $css['table-layout'] = 'fixed';
            } elseif ($style->getLayout() == StyleTable::LAYOUT_AUTO) {
                $css['table-layout'] = 'auto';
            }
        }
        if (is_object($style) && method_exists($style, 'isBidiVisual')) {
            if ($style->isBidiVisual()) {
                $css['direction'] = 'rtl';
            }
        }
        if (is_object($style) && method_exists($style, 'getVAlign')) {
            $css['vertical-align'] = $style->getVAlign();
        }

        foreach (['Top', 'Left', 'Bottom', 'Right'] as $direction) {
            $method = 'getBorder' . $direction . 'Style';
            if (method_exists($style, $method)) {
                $outval = $style->{$method}();
                if ($outval === 'single') {
                    $outval = 'solid';
                }
                if (is_string($outval) && 1 == preg_match('/^[a-z]+$/', $outval)) {
                    $css['border-' . lcfirst($direction) . '-style'] = $outval;
                }
            }

            $method = 'getBorder' . $direction . 'Color';
            if (method_exists($style, $method)) {
                $outval = $style->{$method}();
                if (is_string($outval) && 1 == preg_match('/^[a-z]+$/', $outval)) {
                    $css['border-' . lcfirst($direction) . '-color'] = $outval;
                }
            }

            $method = 'getBorder' . $direction . 'Size';
            if (method_exists($style, $method)) {
                $outval = $style->{$method}();
                if (is_numeric($outval)) {
                    // size is in twips - divide by 20 to get points
                    $css['border-' . lcfirst($direction) . '-width'] = ((string) ($outval / 20)) . 'pt';
                }
            }
        }

        return $this->assembleCss($css);
    }
}
