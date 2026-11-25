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

namespace PhpOffice\PhpWord;

class Autoloader
{
    /** @const string */
    const NAMESPACE_PREFIX = 'PhpOffice\\PhpWord\\';

    public static function register(): void
    {
        spl_autoload_register([new self(), 'autoload']);
    }

    public static function autoload(string $class): void
    {
        $prefixLength = strlen(self::NAMESPACE_PREFIX);
        if (0 === strncmp(self::NAMESPACE_PREFIX, $class, $prefixLength)) {
            $file = str_replace('\\', DIRECTORY_SEPARATOR, substr($class, $prefixLength));
            $file = realpath(__DIR__ . (empty($file) ? '' : DIRECTORY_SEPARATOR) . $file . '.php');
            if (!$file) {
                return;
            }
            if (file_exists($file)) {
                /** @noinspection PhpIncludeInspection Dynamic includes */
                require_once $file;
            }
        }
    }
}
