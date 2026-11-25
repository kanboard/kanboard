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

namespace PhpOffice\PhpWord\Writer\EPub3\Element;

use PhpOffice\PhpWord\Writer\Word2007\Element\AbstractElement as Word2007AbstractElement;

/**
 * Abstract element writer.
 *
 * @since 0.11.0
 */
abstract class AbstractElement extends Word2007AbstractElement
{
    /**
     * Get class name of writer element based on read element.
     *
     * @param \PhpOffice\PhpWord\Element\AbstractElement $element
     */
    public static function getElementClass($element): string
    {
        $elementClass = str_replace('PhpOffice\\PhpWord\\Element\\', '', get_class($element));
        $writerClass = 'PhpOffice\\PhpWord\\Writer\\EPub3\\Element\\' . $elementClass;
        if (!class_exists($writerClass)) {
            throw new \PhpOffice\PhpWord\Exception\Exception("Writer element class {$writerClass} not found.");
        }

        return $writerClass;
    }
}
