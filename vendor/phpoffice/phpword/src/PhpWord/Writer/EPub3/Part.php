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

namespace PhpOffice\PhpWord\Writer\EPub3;

use PhpOffice\PhpWord\Exception\Exception;

/**
 * Factory class for EPub3 parts.
 */
class Part
{
    /**
     * Get the fully qualified class name for a specific part type.
     *
     * @param string $type The type of part (Content, Manifest, Meta, Mimetype)
     *
     * @return string The fully qualified class name
     */
    public static function getPartClass(string $type): string
    {
        $class = 'PhpOffice\\PhpWord\\Writer\\EPub3\\Part\\' . $type;

        if (!class_exists($class)) {
            throw new Exception("Invalid part type: {$type}");
        }

        return $class;
    }
}
