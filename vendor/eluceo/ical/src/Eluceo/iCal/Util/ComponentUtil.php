<?php

/*
 * This file is part of the eluceo/iCal package.
 *
 * (c) Markus Poerschke <markus@eluceo.de>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Eluceo\iCal\Util;

class ComponentUtil
{
    /**
     * Folds a single line.
     *
     * According to RFC 2445, all lines longer than 75 characters will be folded
     *
     * @link http://www.ietf.org/rfc/rfc2445.txt
     *
     * @param $string
     *
     * @return array
     */
    public static function fold($string)
    {
        $lines = array();
        $array = preg_split('/(?<!^)(?!$)/u', $string);

        $line   = '';
        $lineNo = 0;
        foreach ($array as $char) {
            $charLen = strlen($char);
            $lineLen = strlen($line);
            if ($lineLen + $charLen > 75) {
                $line = ' ' . $char;
                ++$lineNo;
            } else {
                $line .= $char;
            }
            $lines[$lineNo] = $line;
        }

        return $lines;
    }
}
