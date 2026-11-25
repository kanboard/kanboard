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

namespace PhpOffice\PhpWord\Shared;

use InvalidArgumentException;
use ReflectionClass;

abstract class AbstractEnum
{
    private static $constCacheArray;

    private static function getConstants()
    {
        if (self::$constCacheArray == null) {
            self::$constCacheArray = [];
        }
        $calledClass = static::class;
        if (!array_key_exists($calledClass, self::$constCacheArray)) {
            $reflect = new ReflectionClass($calledClass);
            self::$constCacheArray[$calledClass] = $reflect->getConstants();
        }

        return self::$constCacheArray[$calledClass];
    }

    /**
     * Returns all values for this enum.
     *
     * @return array
     */
    public static function values()
    {
        return array_values(self::getConstants());
    }

    /**
     * Returns true the value is valid for this enum.
     *
     * @param string $value
     *
     * @return bool true if value is valid
     */
    public static function isValid($value)
    {
        $values = array_values(self::getConstants());

        return in_array($value, $values, true);
    }

    /**
     * Validates that the value passed is a valid value.
     *
     * @param string $value
     */
    public static function validate($value): void
    {
        if (!self::isValid($value)) {
            $calledClass = static::class;
            $values = array_values(self::getConstants());

            throw new InvalidArgumentException("$value is not a valid value for $calledClass, possible values are " . implode(', ', $values));
        }
    }
}
