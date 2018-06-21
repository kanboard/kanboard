<?php
/*
 * PHP QR Code encoder
 *
 * Masking
 *
 * Based on libqrencode C library distributed under LGPL 2.1
 * Copyright (C) 2006, 2007, 2008, 2009 Kentaro Fukuchi <fukuchi@megaui.net>
 *
 * PHP QR Code is distributed under LGPL 3
 * Copyright (C) 2010 Dominik Dzienia <deltalab at poczta dot fm>
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

class QRmask {

    public $runLength = array();

    //----------------------------------------------------------------------
    public function __construct()
    {
        $this->runLength = array_fill(0, Constants::QRSPEC_WIDTH_MAX + 1, 0);
    }

    //----------------------------------------------------------------------
    public function writeFormatInformation($width, &$frame, $mask, $level)
    {
        $blacks = 0;
        $format =  QRspec::getFormatInfo($mask, $level);

        for($i=0; $i<8; $i++) {
            if($format & 1) {
                $blacks += 2;
                $v = 0x85;
            } else {
                $v = 0x84;
            }

            $frame[8][$width - 1 - $i] = chr($v);
            if($i < 6) {
                $frame[$i][8] = chr($v);
            } else {
                $frame[$i + 1][8] = chr($v);
            }
            $format = $format >> 1;
        }

        for($i=0; $i<7; $i++) {
            if($format & 1) {
                $blacks += 2;
                $v = 0x85;
            } else {
                $v = 0x84;
            }

            $frame[$width - 7 + $i][8] = chr($v);
            if($i == 0) {
                $frame[8][7] = chr($v);
            } else {
                $frame[8][6 - $i] = chr($v);
            }

            $format = $format >> 1;
        }

        return $blacks;
    }

    //----------------------------------------------------------------------
    public function mask0($x, $y) { return ($x+$y)&1;                       }
    public function mask1($x, $y) { return ($y&1);                          }
    public function mask2($x, $y) { return ($x%3);                          }
    public function mask3($x, $y) { return ($x+$y)%3;                       }
    public function mask4($x, $y) { return (((int)($y/2))+((int)($x/3)))&1; }
    public function mask5($x, $y) { return (($x*$y)&1)+($x*$y)%3;           }
    public function mask6($x, $y) { return ((($x*$y)&1)+($x*$y)%3)&1;       }
    public function mask7($x, $y) { return ((($x*$y)%3)+(($x+$y)&1))&1;     }

    //----------------------------------------------------------------------
    private function generateMaskNo($maskNo, $width, $frame)
    {
        $bitMask = array_fill(0, $width, array_fill(0, $width, 0));

        for($y=0; $y<$width; $y++) {
            for($x=0; $x<$width; $x++) {
                if(ord($frame[$y][$x]) & 0x80) {
                    $bitMask[$y][$x] = 0;
                } else {
                    $maskFunc = call_user_func(array($this, 'mask'.$maskNo), $x, $y);
                    $bitMask[$y][$x] = ($maskFunc == 0)?1:0;
                }

            }
        }

        return $bitMask;
    }

    //----------------------------------------------------------------------
    public static function serial($bitFrame)
    {
        $codeArr = array();

        foreach ($bitFrame as $line)
            $codeArr[] = join('', $line);

        return gzcompress(join("\n", $codeArr), 9);
    }

    //----------------------------------------------------------------------
    public static function unserial($code)
    {
        $codeArr = array();

        $codeLines = explode("\n", gzuncompress($code));
        foreach ($codeLines as $line)
            $codeArr[] = str_split($line);

        return $codeArr;
    }

    //----------------------------------------------------------------------
    public function makeMaskNo($maskNo, $width, $s, &$d, $maskGenOnly = false)
    {
        $b = 0;
        $bitMask = array();

        $fileName = Constants::QR_CACHE_DIR.'mask_'.$maskNo.DIRECTORY_SEPARATOR.'mask_'.$width.'_'.$maskNo.'.dat';

        if (Constants::QR_CACHEABLE) {
            if (file_exists($fileName)) {
                $bitMask = self::unserial(file_get_contents($fileName));
            } else {
                $bitMask = $this->generateMaskNo($maskNo, $width, $s, $d);
                if (!file_exists(Constants::QR_CACHE_DIR.'mask_'.$maskNo))
                    mkdir(Constants::QR_CACHE_DIR.'mask_'.$maskNo);
                file_put_contents($fileName, self::serial($bitMask));
            }
        } else {
            $bitMask = $this->generateMaskNo($maskNo, $width, $s, $d);
        }

        if ($maskGenOnly)
            return;

        $d = $s;

        for($y=0; $y<$width; $y++) {
            for($x=0; $x<$width; $x++) {
                if($bitMask[$y][$x] == 1) {
                    $d[$y][$x] = chr(ord($s[$y][$x]) ^ (int)$bitMask[$y][$x]);
                }
                $b += (int)(ord($d[$y][$x]) & 1);
            }
        }

        return $b;
    }

    //----------------------------------------------------------------------
    public function makeMask($width, $frame, $maskNo, $level)
    {
        $masked = array_fill(0, $width, str_repeat("\0", $width));
        $this->makeMaskNo($maskNo, $width, $frame, $masked);
        $this->writeFormatInformation($width, $masked, $maskNo, $level);

        return $masked;
    }

    //----------------------------------------------------------------------
    public function calcN1N3($length)
    {
        $demerit = 0;

        for($i=0; $i<$length; $i++) {

            if($this->runLength[$i] >= 5) {
                $demerit += (Constants::N1 + ($this->runLength[$i] - 5));
            }
            if($i & 1) {
                if(($i >= 3) && ($i < ($length-2)) && ($this->runLength[$i] % 3 == 0)) {
                    $fact = (int)($this->runLength[$i] / 3);
                    if(($this->runLength[$i-2] == $fact) &&
                       ($this->runLength[$i-1] == $fact) &&
                       ($this->runLength[$i+1] == $fact) &&
                       ($this->runLength[$i+2] == $fact)) {
                        if(($this->runLength[$i-3] < 0) || ($this->runLength[$i-3] >= (4 * $fact))) {
                            $demerit += Constants::N3;
                        } else if((($i+3) >= $length) || ($this->runLength[$i+3] >= (4 * $fact))) {
                            $demerit += Constants::N3;
                        }
                    }
                }
            }
        }
        return $demerit;
    }

    //----------------------------------------------------------------------
    public function evaluateSymbol($width, $frame)
    {
        $head = 0;
        $demerit = 0;

        for($y=0; $y<$width; $y++) {
            $head = 0;
            $this->runLength[0] = 1;

            $frameY = $frame[$y];

            if ($y>0)
                $frameYM = $frame[$y-1];

            for($x=0; $x<$width; $x++) {
                if(($x > 0) && ($y > 0)) {
                    $b22 = ord($frameY[$x]) & ord($frameY[$x-1]) & ord($frameYM[$x]) & ord($frameYM[$x-1]);
                    $w22 = ord($frameY[$x]) | ord($frameY[$x-1]) | ord($frameYM[$x]) | ord($frameYM[$x-1]);

                    if(($b22 | ($w22 ^ 1))&1) {
                        $demerit += Constants::N2;
                    }
                }
                if(($x == 0) && (ord($frameY[$x]) & 1)) {
                    $this->runLength[0] = -1;
                    $head = 1;
                    $this->runLength[$head] = 1;
                } else if($x > 0) {
                    if((ord($frameY[$x]) ^ ord($frameY[$x-1])) & 1) {
                        $head++;
                        $this->runLength[$head] = 1;
                    } else {
                        $this->runLength[$head]++;
                    }
                }
            }

            $demerit += $this->calcN1N3($head+1);
        }

        for($x=0; $x<$width; $x++) {
            $head = 0;
            $this->runLength[0] = 1;

            for($y=0; $y<$width; $y++) {
                if($y == 0 && (ord($frame[$y][$x]) & 1)) {
                    $this->runLength[0] = -1;
                    $head = 1;
                    $this->runLength[$head] = 1;
                } else if($y > 0) {
                    if((ord($frame[$y][$x]) ^ ord($frame[$y-1][$x])) & 1) {
                        $head++;
                        $this->runLength[$head] = 1;
                    } else {
                        $this->runLength[$head]++;
                    }
                }
            }

            $demerit += $this->calcN1N3($head+1);
        }

        return $demerit;
    }


    //----------------------------------------------------------------------
    public function mask($width, $frame, $level)
    {
        $minDemerit = PHP_INT_MAX;
        $bestMaskNum = 0;
        $bestMask = array();

        $checked_masks = array(0,1,2,3,4,5,6,7);

        if (Constants::QR_FIND_FROM_RANDOM !== false) {

            $howManuOut = 8-(Constants::QR_FIND_FROM_RANDOM % 9);
            for ($i = 0; $i <  $howManuOut; $i++) {
                $remPos = rand (0, count($checked_masks)-1);
                unset($checked_masks[$remPos]);
                $checked_masks = array_values($checked_masks);
            }

        }

        $bestMask = $frame;

        foreach($checked_masks as $i) {
            $mask = array_fill(0, $width, str_repeat("\0", $width));

            $demerit = 0;
            $blacks = 0;
            $blacks  = $this->makeMaskNo($i, $width, $frame, $mask);
            $blacks += $this->writeFormatInformation($width, $mask, $i, $level);
            $blacks  = (int)(100 * $blacks / ($width * $width));
            $demerit = (int)((int)(abs($blacks - 50) / 5) * Constants::N4);
            $demerit += $this->evaluateSymbol($width, $mask);

            if($demerit < $minDemerit) {
                $minDemerit = $demerit;
                $bestMask = $mask;
                $bestMaskNum = $i;
            }
        }

        return $bestMask;
    }

    //----------------------------------------------------------------------
}
