<?php
/*
 * PHP QR Code encoder
 *
 * QR Code specifications
 *
 * Based on libqrencode C library distributed under LGPL 2.1
 * Copyright (C) 2006, 2007, 2008, 2009 Kentaro Fukuchi <fukuchi@megaui.net>
 *
 * PHP QR Code is distributed under LGPL 3
 * Copyright (C) 2010 Dominik Dzienia <deltalab at poczta dot fm>
 *
 * The following data / specifications are taken from
 * "Two dimensional symbol -- QR-code -- Basic Specification" (JIS X0510:2004)
 *  or
 * "Automatic identification and data capture techniques -- 
 *  QR Code 2005 bar code symbology specification" (ISO/IEC 18004:2006)
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */

namespace PHPQRCode;

class QRspec {

    public static $capacity = array(
        array(  0,    0, 0, array(   0,    0,    0,    0)),
        array( 21,   26, 0, array(   7,   10,   13,   17)), // 1
        array( 25,   44, 7, array(  10,   16,   22,   28)),
        array( 29,   70, 7, array(  15,   26,   36,   44)),
        array( 33,  100, 7, array(  20,   36,   52,   64)),
        array( 37,  134, 7, array(  26,   48,   72,   88)), // 5
        array( 41,  172, 7, array(  36,   64,   96,  112)),
        array( 45,  196, 0, array(  40,   72,  108,  130)),
        array( 49,  242, 0, array(  48,   88,  132,  156)),
        array( 53,  292, 0, array(  60,  110,  160,  192)),
        array( 57,  346, 0, array(  72,  130,  192,  224)), //10
        array( 61,  404, 0, array(  80,  150,  224,  264)),
        array( 65,  466, 0, array(  96,  176,  260,  308)),
        array( 69,  532, 0, array( 104,  198,  288,  352)),
        array( 73,  581, 3, array( 120,  216,  320,  384)),
        array( 77,  655, 3, array( 132,  240,  360,  432)), //15
        array( 81,  733, 3, array( 144,  280,  408,  480)),
        array( 85,  815, 3, array( 168,  308,  448,  532)),
        array( 89,  901, 3, array( 180,  338,  504,  588)),
        array( 93,  991, 3, array( 196,  364,  546,  650)),
        array( 97, 1085, 3, array( 224,  416,  600,  700)), //20
        array(101, 1156, 4, array( 224,  442,  644,  750)),
        array(105, 1258, 4, array( 252,  476,  690,  816)),
        array(109, 1364, 4, array( 270,  504,  750,  900)),
        array(113, 1474, 4, array( 300,  560,  810,  960)),
        array(117, 1588, 4, array( 312,  588,  870, 1050)), //25
        array(121, 1706, 4, array( 336,  644,  952, 1110)),
        array(125, 1828, 4, array( 360,  700, 1020, 1200)),
        array(129, 1921, 3, array( 390,  728, 1050, 1260)),
        array(133, 2051, 3, array( 420,  784, 1140, 1350)),
        array(137, 2185, 3, array( 450,  812, 1200, 1440)), //30
        array(141, 2323, 3, array( 480,  868, 1290, 1530)),
        array(145, 2465, 3, array( 510,  924, 1350, 1620)),
        array(149, 2611, 3, array( 540,  980, 1440, 1710)),
        array(153, 2761, 3, array( 570, 1036, 1530, 1800)),
        array(157, 2876, 0, array( 570, 1064, 1590, 1890)), //35
        array(161, 3034, 0, array( 600, 1120, 1680, 1980)),
        array(165, 3196, 0, array( 630, 1204, 1770, 2100)),
        array(169, 3362, 0, array( 660, 1260, 1860, 2220)),
        array(173, 3532, 0, array( 720, 1316, 1950, 2310)),
        array(177, 3706, 0, array( 750, 1372, 2040, 2430)) //40
    );

    //----------------------------------------------------------------------
    public static function getDataLength($version, $level)
    {
        return self::$capacity[$version][Constants::QRCAP_WORDS] - self::$capacity[$version][Constants::QRCAP_EC][$level];
    }

    //----------------------------------------------------------------------
    public static function getECCLength($version, $level)
    {
        return self::$capacity[$version][Constants::QRCAP_EC][$level];
    }

    //----------------------------------------------------------------------
    public static function getWidth($version)
    {
        return self::$capacity[$version][Constants::QRCAP_WIDTH];
    }

    //----------------------------------------------------------------------
    public static function getRemainder($version)
    {
        return self::$capacity[$version][Constants::QRCAP_REMINDER];
    }

    //----------------------------------------------------------------------
    public static function getMinimumVersion($size, $level)
    {

        for($i=1; $i<= Constants::QRSPEC_VERSION_MAX; $i++) {
            $words  = self::$capacity[$i][Constants::QRCAP_WORDS] - self::$capacity[$i][Constants::QRCAP_EC][$level];
            if($words >= $size)
                return $i;
        }

        return -1;
    }

    //######################################################################

    public static $lengthTableBits = array(
        array(10, 12, 14),
        array( 9, 11, 13),
        array( 8, 16, 16),
        array( 8, 10, 12)
    );

    //----------------------------------------------------------------------
    public static function lengthIndicator($mode, $version)
    {
        if ($mode == Constants::QR_MODE_STRUCTURE)
            return 0;

        if ($version <= 9) {
            $l = 0;
        } else if ($version <= 26) {
            $l = 1;
        } else {
            $l = 2;
        }

        return self::$lengthTableBits[$mode][$l];
    }

    //----------------------------------------------------------------------
    public static function maximumWords($mode, $version)
    {
        if($mode == Constants::QR_MODE_STRUCTURE)
            return 3;

        if($version <= 9) {
            $l = 0;
        } else if($version <= 26) {
            $l = 1;
        } else {
            $l = 2;
        }

        $bits = self::$lengthTableBits[$mode][$l];
        $words = (1 << $bits) - 1;

        if($mode == Constants::QR_MODE_KANJI) {
            $words *= 2; // the number of bytes is required
        }

        return $words;
    }

    // Error correction code -----------------------------------------------
    // Table of the error correction code (Reed-Solomon block)
    // See Table 12-16 (pp.30-36), JIS X0510:2004.

    public static $eccTable = array(
        array(array( 0,  0), array( 0,  0), array( 0,  0), array( 0,  0)),
        array(array( 1,  0), array( 1,  0), array( 1,  0), array( 1,  0)), // 1
        array(array( 1,  0), array( 1,  0), array( 1,  0), array( 1,  0)),
        array(array( 1,  0), array( 1,  0), array( 2,  0), array( 2,  0)),
        array(array( 1,  0), array( 2,  0), array( 2,  0), array( 4,  0)),
        array(array( 1,  0), array( 2,  0), array( 2,  2), array( 2,  2)), // 5
        array(array( 2,  0), array( 4,  0), array( 4,  0), array( 4,  0)),
        array(array( 2,  0), array( 4,  0), array( 2,  4), array( 4,  1)),
        array(array( 2,  0), array( 2,  2), array( 4,  2), array( 4,  2)),
        array(array( 2,  0), array( 3,  2), array( 4,  4), array( 4,  4)),
        array(array( 2,  2), array( 4,  1), array( 6,  2), array( 6,  2)), //10
        array(array( 4,  0), array( 1,  4), array( 4,  4), array( 3,  8)),
        array(array( 2,  2), array( 6,  2), array( 4,  6), array( 7,  4)),
        array(array( 4,  0), array( 8,  1), array( 8,  4), array(12,  4)),
        array(array( 3,  1), array( 4,  5), array(11,  5), array(11,  5)),
        array(array( 5,  1), array( 5,  5), array( 5,  7), array(11,  7)), //15
        array(array( 5,  1), array( 7,  3), array(15,  2), array( 3, 13)),
        array(array( 1,  5), array(10,  1), array( 1, 15), array( 2, 17)),
        array(array( 5,  1), array( 9,  4), array(17,  1), array( 2, 19)),
        array(array( 3,  4), array( 3, 11), array(17,  4), array( 9, 16)),
        array(array( 3,  5), array( 3, 13), array(15,  5), array(15, 10)), //20
        array(array( 4,  4), array(17,  0), array(17,  6), array(19,  6)),
        array(array( 2,  7), array(17,  0), array( 7, 16), array(34,  0)),
        array(array( 4,  5), array( 4, 14), array(11, 14), array(16, 14)),
        array(array( 6,  4), array( 6, 14), array(11, 16), array(30,  2)),
        array(array( 8,  4), array( 8, 13), array( 7, 22), array(22, 13)), //25
        array(array(10,  2), array(19,  4), array(28,  6), array(33,  4)),
        array(array( 8,  4), array(22,  3), array( 8, 26), array(12, 28)),
        array(array( 3, 10), array( 3, 23), array( 4, 31), array(11, 31)),
        array(array( 7,  7), array(21,  7), array( 1, 37), array(19, 26)),
        array(array( 5, 10), array(19, 10), array(15, 25), array(23, 25)), //30
        array(array(13,  3), array( 2, 29), array(42,  1), array(23, 28)),
        array(array(17,  0), array(10, 23), array(10, 35), array(19, 35)),
        array(array(17,  1), array(14, 21), array(29, 19), array(11, 46)),
        array(array(13,  6), array(14, 23), array(44,  7), array(59,  1)),
        array(array(12,  7), array(12, 26), array(39, 14), array(22, 41)), //35
        array(array( 6, 14), array( 6, 34), array(46, 10), array( 2, 64)),
        array(array(17,  4), array(29, 14), array(49, 10), array(24, 46)),
        array(array( 4, 18), array(13, 32), array(48, 14), array(42, 32)),
        array(array(20,  4), array(40,  7), array(43, 22), array(10, 67)),
        array(array(19,  6), array(18, 31), array(34, 34), array(20, 61)),//40
    );

    //----------------------------------------------------------------------
    // CACHEABLE!!!

    public static function getEccSpec($version, $level, array &$spec)
    {
        if (count($spec) < 5) {
            $spec = array(0,0,0,0,0);
        }

        $b1   = self::$eccTable[$version][$level][0];
        $b2   = self::$eccTable[$version][$level][1];
        $data = self::getDataLength($version, $level);
        $ecc  = self::getECCLength($version, $level);

        if($b2 == 0) {
            $spec[0] = $b1;
            $spec[1] = (int)($data / $b1);
            $spec[2] = (int)($ecc / $b1);
            $spec[3] = 0;
            $spec[4] = 0;
        } else {
            $spec[0] = $b1;
            $spec[1] = (int)($data / ($b1 + $b2));
            $spec[2] = (int)($ecc  / ($b1 + $b2));
            $spec[3] = $b2;
            $spec[4] = $spec[1] + 1;
        }
    }

    // Alignment pattern ---------------------------------------------------

    // Positions of alignment patterns.
    // This array includes only the second and the third position of the
    // alignment patterns. Rest of them can be calculated from the distance
    // between them.

    // See Table 1 in Appendix E (pp.71) of JIS X0510:2004.

    public static $alignmentPattern = array(
        array( 0,  0),
        array( 0,  0), array(18,  0), array(22,  0), array(26,  0), array(30,  0), // 1- 5
        array(34,  0), array(22, 38), array(24, 42), array(26, 46), array(28, 50), // 6-10
        array(30, 54), array(32, 58), array(34, 62), array(26, 46), array(26, 48), //11-15
        array(26, 50), array(30, 54), array(30, 56), array(30, 58), array(34, 62), //16-20
        array(28, 50), array(26, 50), array(30, 54), array(28, 54), array(32, 58), //21-25
        array(30, 58), array(34, 62), array(26, 50), array(30, 54), array(26, 52), //26-30
        array(30, 56), array(34, 60), array(30, 58), array(34, 62), array(30, 54), //31-35
        array(24, 50), array(28, 54), array(32, 58), array(26, 54), array(30, 58), //35-40
    );


    /** --------------------------------------------------------------------
     * Put an alignment marker.
     * @param frame
     * @param width
     * @param ox,oy center coordinate of the pattern
     */
    public static function putAlignmentMarker(array &$frame, $ox, $oy)
    {
        $finder = array(
            "\xa1\xa1\xa1\xa1\xa1",
            "\xa1\xa0\xa0\xa0\xa1",
            "\xa1\xa0\xa1\xa0\xa1",
            "\xa1\xa0\xa0\xa0\xa1",
            "\xa1\xa1\xa1\xa1\xa1"
        );

        $yStart = $oy-2;
        $xStart = $ox-2;

        for($y=0; $y<5; $y++) {
            QRstr::set($frame, $xStart, $yStart+$y, $finder[$y]);
        }
    }

    //----------------------------------------------------------------------
    public static function putAlignmentPattern($version, &$frame, $width)
    {
        if($version < 2)
            return;

        $d = self::$alignmentPattern[$version][1] - self::$alignmentPattern[$version][0];
        if($d < 0) {
            $w = 2;
        } else {
            $w = (int)(($width - self::$alignmentPattern[$version][0]) / $d + 2);
        }

        if($w * $w - 3 == 1) {
            $x = self::$alignmentPattern[$version][0];
            $y = self::$alignmentPattern[$version][0];
            self::putAlignmentMarker($frame, $x, $y);
            return;
        }

        $cx = self::$alignmentPattern[$version][0];
        for($x=1; $x<$w - 1; $x++) {
            self::putAlignmentMarker($frame, 6, $cx);
            self::putAlignmentMarker($frame, $cx,  6);
            $cx += $d;
        }

        $cy = self::$alignmentPattern[$version][0];
        for($y=0; $y<$w-1; $y++) {
            $cx = self::$alignmentPattern[$version][0];
            for($x=0; $x<$w-1; $x++) {
                self::putAlignmentMarker($frame, $cx, $cy);
                $cx += $d;
            }
            $cy += $d;
        }
    }

    // Version information pattern -----------------------------------------

    // Version information pattern (BCH coded).
    // See Table 1 in Appendix D (pp.68) of JIS X0510:2004.

    // size: [Constants::QRSPEC_VERSION_MAX - 6]

    public static $versionPattern = array(
        0x07c94, 0x085bc, 0x09a99, 0x0a4d3, 0x0bbf6, 0x0c762, 0x0d847, 0x0e60d,
        0x0f928, 0x10b78, 0x1145d, 0x12a17, 0x13532, 0x149a6, 0x15683, 0x168c9,
        0x177ec, 0x18ec4, 0x191e1, 0x1afab, 0x1b08e, 0x1cc1a, 0x1d33f, 0x1ed75,
        0x1f250, 0x209d5, 0x216f0, 0x228ba, 0x2379f, 0x24b0b, 0x2542e, 0x26a64,
        0x27541, 0x28c69
    );

    //----------------------------------------------------------------------
    public static function getVersionPattern($version)
    {
        if($version < 7 || $version > Constants::QRSPEC_VERSION_MAX)
            return 0;

        return self::$versionPattern[$version -7];
    }

    // Format information --------------------------------------------------
    // See calcFormatInfo in tests/test_qrspec.c (orginal qrencode c lib)

    public static $formatInfo = array(
        array(0x77c4, 0x72f3, 0x7daa, 0x789d, 0x662f, 0x6318, 0x6c41, 0x6976),
        array(0x5412, 0x5125, 0x5e7c, 0x5b4b, 0x45f9, 0x40ce, 0x4f97, 0x4aa0),
        array(0x355f, 0x3068, 0x3f31, 0x3a06, 0x24b4, 0x2183, 0x2eda, 0x2bed),
        array(0x1689, 0x13be, 0x1ce7, 0x19d0, 0x0762, 0x0255, 0x0d0c, 0x083b)
    );

    public static function getFormatInfo($mask, $level)
    {
        if($mask < 0 || $mask > 7)
            return 0;

        if($level < 0 || $level > 3)
            return 0;

        return self::$formatInfo[$level][$mask];
    }

    // Frame ---------------------------------------------------------------
    // Cache of initial frames.

    public static $frames = array();

    /** --------------------------------------------------------------------
     * Put a finder pattern.
     * @param frame
     * @param width
     * @param ox,oy upper-left coordinate of the pattern
     */
    public static function putFinderPattern(&$frame, $ox, $oy)
    {
        $finder = array(
            "\xc1\xc1\xc1\xc1\xc1\xc1\xc1",
            "\xc1\xc0\xc0\xc0\xc0\xc0\xc1",
            "\xc1\xc0\xc1\xc1\xc1\xc0\xc1",
            "\xc1\xc0\xc1\xc1\xc1\xc0\xc1",
            "\xc1\xc0\xc1\xc1\xc1\xc0\xc1",
            "\xc1\xc0\xc0\xc0\xc0\xc0\xc1",
            "\xc1\xc1\xc1\xc1\xc1\xc1\xc1"
        );

        for($y=0; $y<7; $y++) {
            QRstr::set($frame, $ox, $oy+$y, $finder[$y]);
        }
    }

    //----------------------------------------------------------------------
    public static function createFrame($version)
    {
        $width = self::$capacity[$version][Constants::QRCAP_WIDTH];
        $frameLine = str_repeat ("\0", $width);
        $frame = array_fill(0, $width, $frameLine);

        // Finder pattern
        self::putFinderPattern($frame, 0, 0);
        self::putFinderPattern($frame, $width - 7, 0);
        self::putFinderPattern($frame, 0, $width - 7);

        // Separator
        $yOffset = $width - 7;

        for($y=0; $y<7; $y++) {
            $frame[$y][7] = "\xc0";
            $frame[$y][$width - 8] = "\xc0";
            $frame[$yOffset][7] = "\xc0";
            $yOffset++;
        }

        $setPattern = str_repeat("\xc0", 8);

        QRstr::set($frame, 0, 7, $setPattern);
        QRstr::set($frame, $width-8, 7, $setPattern);
        QRstr::set($frame, 0, $width - 8, $setPattern);

        // Format info
        $setPattern = str_repeat("\x84", 9);
        QRstr::set($frame, 0, 8, $setPattern);
        QRstr::set($frame, $width - 8, 8, $setPattern, 8);

        $yOffset = $width - 8;

        for($y=0; $y<8; $y++,$yOffset++) {
            $frame[$y][8] = "\x84";
            $frame[$yOffset][8] = "\x84";
        }

        // Timing pattern

        for($i=1; $i<$width-15; $i++) {
            $frame[6][7+$i] = chr(0x90 | ($i & 1));
            $frame[7+$i][6] = chr(0x90 | ($i & 1));
        }

        // Alignment pattern
        self::putAlignmentPattern($version, $frame, $width);

        // Version information
        if($version >= 7) {
            $vinf = self::getVersionPattern($version);

            $v = $vinf;

            for($x=0; $x<6; $x++) {
                for($y=0; $y<3; $y++) {
                    $frame[($width - 11)+$y][$x] = chr(0x88 | ($v & 1));
                    $v = $v >> 1;
                }
            }

            $v = $vinf;
            for($y=0; $y<6; $y++) {
                for($x=0; $x<3; $x++) {
                    $frame[$y][$x+($width - 11)] = chr(0x88 | ($v & 1));
                    $v = $v >> 1;
                }
            }
        }

        // and a little bit...
        $frame[$width - 8][8] = "\x81";

        return $frame;
    }

    //----------------------------------------------------------------------
    public static function debug($frame, $binary_mode = false)
    {
        if ($binary_mode) {

                foreach ($frame as &$frameLine) {
                    $frameLine = join('<span class="m">&nbsp;&nbsp;</span>', explode('0', $frameLine));
                    $frameLine = join('&#9608;&#9608;', explode('1', $frameLine));
                }

                ?>
            <style>
                .m { background-color: white; }
            </style>
            <?php
                echo '<pre><tt><br/ ><br/ ><br/ >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                echo join("<br/ >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $frame);
                echo '</tt></pre><br/ ><br/ ><br/ ><br/ ><br/ ><br/ >';

        } else {

            foreach ($frame as &$frameLine) {
                $frameLine = join('<span class="m">&nbsp;</span>',  explode("\xc0", $frameLine));
                $frameLine = join('<span class="m">&#9618;</span>', explode("\xc1", $frameLine));
                $frameLine = join('<span class="p">&nbsp;</span>',  explode("\xa0", $frameLine));
                $frameLine = join('<span class="p">&#9618;</span>', explode("\xa1", $frameLine));
                $frameLine = join('<span class="s">&#9671;</span>', explode("\x84", $frameLine)); //format 0
                $frameLine = join('<span class="s">&#9670;</span>', explode("\x85", $frameLine)); //format 1
                $frameLine = join('<span class="x">&#9762;</span>', explode("\x81", $frameLine)); //special bit
                $frameLine = join('<span class="c">&nbsp;</span>',  explode("\x90", $frameLine)); //clock 0
                $frameLine = join('<span class="c">&#9719;</span>', explode("\x91", $frameLine)); //clock 1
                $frameLine = join('<span class="f">&nbsp;</span>',  explode("\x88", $frameLine)); //version
                $frameLine = join('<span class="f">&#9618;</span>', explode("\x89", $frameLine)); //version
                $frameLine = join('&#9830;', explode("\x01", $frameLine));
                $frameLine = join('&#8901;', explode("\0", $frameLine));
            }

            ?>
            <style>
                .p { background-color: yellow; }
                .m { background-color: #00FF00; }
                .s { background-color: #FF0000; }
                .c { background-color: aqua; }
                .x { background-color: pink; }
                .f { background-color: gold; }
            </style>
            <?php
            echo "<pre><tt>";
            echo join("<br/ >", $frame);
            echo "</tt></pre>";

        }
    }

    //----------------------------------------------------------------------
    public static function serial($frame)
    {
        return gzcompress(join("\n", $frame), 9);
    }

    //----------------------------------------------------------------------
    public static function unserial($code)
    {
        return explode("\n", gzuncompress($code));
    }

    //----------------------------------------------------------------------
    public static function newFrame($version)
    {
        if($version < 1 || $version > Constants::QRSPEC_VERSION_MAX)
            return null;

        if(!isset(self::$frames[$version])) {

            $fileName = Constants::QR_CACHE_DIR.'frame_'.$version.'.dat';

            if (Constants::QR_CACHEABLE) {
                if (file_exists($fileName)) {
                    self::$frames[$version] = self::unserial(file_get_contents($fileName));
                } else {
                    self::$frames[$version] = self::createFrame($version);
                    file_put_contents($fileName, self::serial(self::$frames[$version]));
                }
            } else {
                self::$frames[$version] = self::createFrame($version);
            }
        }

        if(is_null(self::$frames[$version]))
            return null;

        return self::$frames[$version];
    }

    //----------------------------------------------------------------------
    public static function rsBlockNum($spec)     { return $spec[0] + $spec[3]; }
    public static function rsBlockNum1($spec)    { return $spec[0]; }
    public static function rsDataCodes1($spec)   { return $spec[1]; }
    public static function rsEccCodes1($spec)    { return $spec[2]; }
    public static function rsBlockNum2($spec)    { return $spec[3]; }
    public static function rsDataCodes2($spec)   { return $spec[4]; }
    public static function rsEccCodes2($spec)    { return $spec[2]; }
    public static function rsDataLength($spec)   { return ($spec[0] * $spec[1]) + ($spec[3] * $spec[4]);    }
    public static function rsEccLength($spec)    { return ($spec[0] + $spec[3]) * $spec[2]; }

}