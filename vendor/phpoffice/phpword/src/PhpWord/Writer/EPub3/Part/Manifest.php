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

namespace PhpOffice\PhpWord\Writer\EPub3\Part;

/**
 * Class for EPub3 manifest part.
 */
class Manifest extends AbstractPart
{
    /**
     * Write part content.
     */
    public function write(): string
    {
        $content = '<?xml version="1.0" encoding="UTF-8"?>';
        $content .= '<container version="1.0" xmlns="urn:oasis:names:tc:opendocument:xmlns:container">';
        $content .= '<rootfiles>';
        $content .= '<rootfile full-path="content.opf" media-type="application/oebps-package+xml"/>';
        $content .= '</rootfiles>';
        $content .= '</container>';

        return $content;
    }
}
