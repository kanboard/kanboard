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

namespace PhpOffice\PhpWord\Writer\EPub3\Style;

/**
 * Class for EPub3 table styles.
 */
class Table extends AbstractStyle
{
    /**
     * Write style content.
     */
    public function write(): string
    {
        $content = 'table {';
        $content .= 'border-collapse: collapse;';
        $content .= 'width: 100%;';
        $content .= '}';
        $content .= 'th, td {';
        $content .= 'border: 1px solid black;';
        $content .= 'padding: 8px;';
        $content .= 'text-align: left;';
        $content .= '}';

        return $content;
    }
}
