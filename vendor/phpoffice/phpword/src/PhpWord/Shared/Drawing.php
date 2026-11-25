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

/**
 * Drawing.
 */
class Drawing
{
    const DPI_96 = 96;

    /**
     * Convert pixels to EMU.
     *
     * @param  int $pValue Value in pixels
     *
     * @return int
     */
    public static function pixelsToEmu($pValue = 0)
    {
        return round($pValue * 9525);
    }

    /**
     * Convert EMU to pixels.
     *
     * @param  int $pValue Value in EMU
     *
     * @return int
     */
    public static function emuToPixels($pValue = 0)
    {
        if ($pValue == 0) {
            return 0;
        }

        return round($pValue / 9525);
    }

    /**
     * Convert pixels to points.
     *
     * @param  int $pValue Value in pixels
     *
     * @return float
     */
    public static function pixelsToPoints($pValue = 0)
    {
        return $pValue * 0.67777777;
    }

    /**
     * Convert points width to centimeters.
     *
     * @param  int $pValue Value in points
     *
     * @return float
     */
    public static function pointsToCentimeters($pValue = 0)
    {
        if ($pValue == 0) {
            return 0;
        }

        return (($pValue * 1.333333333) / self::DPI_96) * 2.54;
    }

    /**
     * Convert points width to pixels.
     *
     * @param  int $pValue Value in points
     *
     * @return float
     */
    public static function pointsToPixels($pValue = 0)
    {
        if ($pValue == 0) {
            return 0;
        }

        return $pValue * 1.333333333;
    }

    /**
     * Convert pixels to centimeters.
     *
     * @param  int $pValue Value in pixels
     *
     * @return float
     */
    public static function pixelsToCentimeters($pValue = 0)
    {
        //return $pValue * 0.028;
        return ($pValue / self::DPI_96) * 2.54;
    }

    /**
     * Convert centimeters width to pixels.
     *
     * @param  int $pValue Value in centimeters
     *
     * @return float
     */
    public static function centimetersToPixels($pValue = 0)
    {
        if ($pValue == 0) {
            return 0;
        }

        return ($pValue / 2.54) * self::DPI_96;
    }

    /**
     * Convert degrees to angle.
     *
     * @param  int $pValue Degrees
     *
     * @return int
     */
    public static function degreesToAngle($pValue = 0)
    {
        return (int) round($pValue * 60000);
    }

    /**
     * Convert angle to degrees.
     *
     * @param  int $pValue Angle
     *
     * @return int
     */
    public static function angleToDegrees($pValue = 0)
    {
        if ($pValue == 0) {
            return 0;
        }

        return round($pValue / 60000);
    }

    /**
     * Convert centimeters width to twips.
     *
     * @param int $pValue
     *
     * @return float
     */
    public static function centimetersToTwips($pValue = 0)
    {
        if ($pValue == 0) {
            return 0;
        }

        return $pValue * 566.928;
    }

    /**
     * Convert twips width to centimeters.
     *
     * @param int $pValue
     *
     * @return float
     */
    public static function twipsToCentimeters($pValue = 0)
    {
        if ($pValue == 0) {
            return 0;
        }

        return $pValue / 566.928;
    }

    /**
     * Convert inches width to twips.
     *
     * @param int $pValue
     *
     * @return float
     */
    public static function inchesToTwips($pValue = 0)
    {
        if ($pValue == 0) {
            return 0;
        }

        return $pValue * 1440;
    }

    /**
     * Convert twips width to inches.
     *
     * @param int $pValue
     *
     * @return float
     */
    public static function twipsToInches($pValue = 0)
    {
        if ($pValue == 0) {
            return 0;
        }

        return $pValue / 1440;
    }

    /**
     * Convert twips width to pixels.
     *
     * @param int $pValue
     *
     * @return float
     */
    public static function twipsToPixels($pValue = 0)
    {
        if ($pValue == 0) {
            return 0;
        }

        return round($pValue / 15.873984);
    }

    /**
     * Convert HTML hexadecimal to RGB.
     *
     * @param string $pValue HTML Color in hexadecimal
     *
     * @return array|false Value in RGB
     */
    public static function htmlToRGB($pValue)
    {
        if ($pValue[0] == '#') {
            $pValue = substr($pValue, 1);
        }

        if (strlen($pValue) == 6) {
            [$colorR, $colorG, $colorB] = [$pValue[0] . $pValue[1], $pValue[2] . $pValue[3], $pValue[4] . $pValue[5]];
        } elseif (strlen($pValue) == 3) {
            [$colorR, $colorG, $colorB] = [$pValue[0] . $pValue[0], $pValue[1] . $pValue[1], $pValue[2] . $pValue[2]];
        } else {
            return false;
        }

        $colorR = hexdec($colorR);
        $colorG = hexdec($colorG);
        $colorB = hexdec($colorB);

        return [$colorR, $colorG, $colorB];
    }
}
