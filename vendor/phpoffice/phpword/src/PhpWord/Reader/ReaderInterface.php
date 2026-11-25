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

namespace PhpOffice\PhpWord\Reader;

/**
 * Reader interface.
 *
 * @since 0.8.0
 */
interface ReaderInterface
{
    /**
     * Can the current ReaderInterface read the file?
     *
     * @param  string $filename
     *
     * @return bool
     */
    public function canRead($filename);

    /**
     * Loads PhpWord from file.
     *
     * @param string $filename
     */
    public function load($filename);
}
