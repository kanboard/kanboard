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

namespace PhpOffice\PhpWord\Exception;

/**
 * @since 0.12.0
 */
final class CopyFileException extends Exception
{
    /**
     * @param string $source The fully qualified source file name
     * @param string $destination The fully qualified destination file name
     * @param int $code The user defined exception code
     * @param \Exception $previous The previous exception used for the exception chaining
     */
    public function __construct($source, $destination, $code = 0, ?\Exception $previous = null)
    {
        parent::__construct(
            sprintf('Could not copy \'%s\' file to \'%s\'.', $source, $destination),
            $code,
            $previous
        );
    }
}
