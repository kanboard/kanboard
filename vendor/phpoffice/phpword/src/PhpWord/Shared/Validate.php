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

namespace PhpOffice\PhpWord\Shared;

class Validate
{
    public const CSS_WHITESPACE = [
        'pre-wrap',
        'normal',
        'nowrap',
        'pre',
        'pre-line',
        'initial',
        'inherit',
    ];

    public const CSS_GENERICFONT = [
        'serif',
        'sans-serif',
        'monospace',
        'cursive',
        'fantasy',
        'system-ui',
        'math',
        'emoji',
        'fangsong',
    ];

    /**
     * Validate html css white-space value. It is expected that only pre-wrap and normal (default) are useful.
     *
     * @param string $value CSS White space
     *
     * @return string value if valid, empty string if not
     */
    public static function validateCSSWhiteSpace(?string $value): string
    {
        if (in_array($value, self::CSS_WHITESPACE)) {
            return $value;
        }

        return '';
    }

    /**
     * Validate generic font for fallback for html.
     *
     * @param string $value Generic font name
     *
     * @return string Value if legitimate, empty string if not
     */
    public static function validateCSSGenericFont(?string $value): string
    {
        if (in_array($value, self::CSS_GENERICFONT)) {
            return $value;
        }

        return '';
    }
}
