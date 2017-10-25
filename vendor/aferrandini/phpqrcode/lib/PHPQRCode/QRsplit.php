<?php
/*
 * PHP QR Code encoder
 *
 * Input splitting classes
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

use Exception;

class QRsplit {

    public $dataStr = '';
    public $input;
    public $modeHint;

    //----------------------------------------------------------------------
    public function __construct($dataStr, $input, $modeHint)
    {
        $this->dataStr  = $dataStr;
        $this->input    = $input;
        $this->modeHint = $modeHint;
    }

    //----------------------------------------------------------------------
    public static function isdigitat($str, $pos)
    {
        if ($pos >= strlen($str))
            return false;

        return ((ord($str[$pos]) >= ord('0'))&&(ord($str[$pos]) <= ord('9')));
    }

    //----------------------------------------------------------------------
    public static function isalnumat($str, $pos)
    {
        if ($pos >= strlen($str))
            return false;

        return (QRinput::lookAnTable(ord($str[$pos])) >= 0);
    }

    //----------------------------------------------------------------------
    public function identifyMode($pos)
    {
        if ($pos >= strlen($this->dataStr))
            return Constants::QR_MODE_NUL;

        $c = $this->dataStr[$pos];

        if(self::isdigitat($this->dataStr, $pos)) {
            return Constants::QR_MODE_NUM;
        } else if(self::isalnumat($this->dataStr, $pos)) {
            return Constants::QR_MODE_AN;
        } else if($this->modeHint == Constants::QR_MODE_KANJI) {

            if ($pos+1 < strlen($this->dataStr))
            {
                $d = $this->dataStr[$pos+1];
                $word = (ord($c) << 8) | ord($d);
                if(($word >= 0x8140 && $word <= 0x9ffc) || ($word >= 0xe040 && $word <= 0xebbf)) {
                    return Constants::QR_MODE_KANJI;
                }
            }
        }

        return Constants::QR_MODE_8;
    }

    //----------------------------------------------------------------------
    public function eatNum()
    {
        $ln = QRspec::lengthIndicator(Constants::QR_MODE_NUM, $this->input->getVersion());

        $p = 0;
        while(self::isdigitat($this->dataStr, $p)) {
            $p++;
        }

        $run = $p;
        $mode = $this->identifyMode($p);

        if($mode == Constants::QR_MODE_8) {
            $dif = QRinput::estimateBitsModeNum($run) + 4 + $ln
                 + QRinput::estimateBitsMode8(1)         // + 4 + l8
                 - QRinput::estimateBitsMode8($run + 1); // - 4 - l8
            if($dif > 0) {
                return $this->eat8();
            }
        }
        if($mode == Constants::QR_MODE_AN) {
            $dif = QRinput::estimateBitsModeNum($run) + 4 + $ln
                 + QRinput::estimateBitsModeAn(1)        // + 4 + la
                 - QRinput::estimateBitsModeAn($run + 1);// - 4 - la
            if($dif > 0) {
                return $this->eatAn();
            }
        }

        $ret = $this->input->append(Constants::QR_MODE_NUM, $run, str_split($this->dataStr));
        if($ret < 0)
            return -1;

        return $run;
    }

    //----------------------------------------------------------------------
    public function eatAn()
    {
        $la = QRspec::lengthIndicator(Constants::QR_MODE_AN,  $this->input->getVersion());
        $ln = QRspec::lengthIndicator(Constants::QR_MODE_NUM, $this->input->getVersion());

        $p = 0;

        while(self::isalnumat($this->dataStr, $p)) {
            if(self::isdigitat($this->dataStr, $p)) {
                $q = $p;
                while(self::isdigitat($this->dataStr, $q)) {
                    $q++;
                }

                $dif = QRinput::estimateBitsModeAn($p) // + 4 + la
                     + QRinput::estimateBitsModeNum($q - $p) + 4 + $ln
                     - QRinput::estimateBitsModeAn($q); // - 4 - la

                if($dif < 0) {
                    break;
                } else {
                    $p = $q;
                }
            } else {
                $p++;
            }
        }

        $run = $p;

        if(!self::isalnumat($this->dataStr, $p)) {
            $dif = QRinput::estimateBitsModeAn($run) + 4 + $la
                 + QRinput::estimateBitsMode8(1) // + 4 + l8
                 - QRinput::estimateBitsMode8($run + 1); // - 4 - l8
            if($dif > 0) {
                return $this->eat8();
            }
        }

        $ret = $this->input->append(Constants::QR_MODE_AN, $run, str_split($this->dataStr));
        if($ret < 0)
            return -1;

        return $run;
    }

    //----------------------------------------------------------------------
    public function eatKanji()
    {
        $p = 0;

        while($this->identifyMode($p) == Constants::QR_MODE_KANJI) {
            $p += 2;
        }

        $ret = $this->input->append(Constants::QR_MODE_KANJI, $p, str_split($this->dataStr));
        if($ret < 0)
            return -1;

        return $ret;
    }

    //----------------------------------------------------------------------
    public function eat8()
    {
        $la = QRspec::lengthIndicator(Constants::QR_MODE_AN, $this->input->getVersion());
        $ln = QRspec::lengthIndicator(Constants::QR_MODE_NUM, $this->input->getVersion());

        $p = 1;
        $dataStrLen = strlen($this->dataStr);

        while($p < $dataStrLen) {

            $mode = $this->identifyMode($p);
            if($mode == Constants::QR_MODE_KANJI) {
                break;
            }
            if($mode == Constants::QR_MODE_NUM) {
                $q = $p;
                while(self::isdigitat($this->dataStr, $q)) {
                    $q++;
                }
                $dif = QRinput::estimateBitsMode8($p) // + 4 + l8
                     + QRinput::estimateBitsModeNum($q - $p) + 4 + $ln
                     - QRinput::estimateBitsMode8($q); // - 4 - l8
                if($dif < 0) {
                    break;
                } else {
                    $p = $q;
                }
            } else if($mode == Constants::QR_MODE_AN) {
                $q = $p;
                while(self::isalnumat($this->dataStr, $q)) {
                    $q++;
                }
                $dif = QRinput::estimateBitsMode8($p)  // + 4 + l8
                     + QRinput::estimateBitsModeAn($q - $p) + 4 + $la
                     - QRinput::estimateBitsMode8($q); // - 4 - l8
                if($dif < 0) {
                    break;
                } else {
                    $p = $q;
                }
            } else {
                $p++;
            }
        }

        $run = $p;
        $ret = $this->input->append(Constants::QR_MODE_8, $run, str_split($this->dataStr));

        if($ret < 0)
            return -1;

        return $run;
    }

    //----------------------------------------------------------------------
    public function splitString()
    {
        while (strlen($this->dataStr) > 0)
        {
            if($this->dataStr == '')
                return 0;

            $mode = $this->identifyMode(0);

            switch ($mode) {
                case Constants::QR_MODE_NUM: $length = $this->eatNum(); break;
                case Constants::QR_MODE_AN:  $length = $this->eatAn(); break;
                case Constants::QR_MODE_KANJI:
                    if ($hint == Constants::QR_MODE_KANJI)
                            $length = $this->eatKanji();
                    else    $length = $this->eat8();
                    break;
                default: $length = $this->eat8(); break;

            }

            if($length == 0) return 0;
            if($length < 0)  return -1;

            $this->dataStr = substr($this->dataStr, $length);
        }
    }

    //----------------------------------------------------------------------
    public function toUpper()
    {
        $stringLen = strlen($this->dataStr);
        $p = 0;

        while ($p<$stringLen) {
            $mode = self::identifyMode(substr($this->dataStr, $p), $this->modeHint);
            if($mode == Constants::QR_MODE_KANJI) {
                $p += 2;
            } else {
                if (ord($this->dataStr[$p]) >= ord('a') && ord($this->dataStr[$p]) <= ord('z')) {
                    $this->dataStr[$p] = chr(ord($this->dataStr[$p]) - 32);
                }
                $p++;
            }
        }

        return $this->dataStr;
    }

    //----------------------------------------------------------------------
    public static function splitStringToQRinput($string, QRinput $input, $modeHint, $casesensitive = true)
    {
        if(is_null($string) || $string == '\0' || $string == '') {
            throw new Exception('empty string!!!');
        }

        $split = new QRsplit($string, $input, $modeHint);

        if(!$casesensitive)
            $split->toUpper();

        return $split->splitString();
    }
}
