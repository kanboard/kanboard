<?php
/*
 * PHP QR Code encoder
 *
 * Bitstream class
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

class QRbitstream {

    public $data = array();

    //----------------------------------------------------------------------
    public function size()
    {
        return count($this->data);
    }

    //----------------------------------------------------------------------
    public function allocate($setLength)
    {
        $this->data = array_fill(0, $setLength, 0);
        return 0;
    }

    //----------------------------------------------------------------------
    public static function newFromNum($bits, $num)
    {
        $bstream = new QRbitstream();
        $bstream->allocate($bits);

        $mask = 1 << ($bits - 1);
        for($i=0; $i<$bits; $i++) {
            if($num & $mask) {
                $bstream->data[$i] = 1;
            } else {
                $bstream->data[$i] = 0;
            }
            $mask = $mask >> 1;
        }

        return $bstream;
    }

    //----------------------------------------------------------------------
    public static function newFromBytes($size, $data)
    {
        $bstream = new QRbitstream();
        $bstream->allocate($size * 8);
        $p=0;

        for($i=0; $i<$size; $i++) {
            $mask = 0x80;
            for($j=0; $j<8; $j++) {
                if($data[$i] & $mask) {
                    $bstream->data[$p] = 1;
                } else {
                    $bstream->data[$p] = 0;
                }
                $p++;
                $mask = $mask >> 1;
            }
        }

        return $bstream;
    }

    //----------------------------------------------------------------------
    public function append(QRbitstream $arg)
    {
        if (is_null($arg)) {
            return -1;
        }

        if($arg->size() == 0) {
            return 0;
        }

        if($this->size() == 0) {
            $this->data = $arg->data;
            return 0;
        }

        $this->data = array_values(array_merge($this->data, $arg->data));

        return 0;
    }

    //----------------------------------------------------------------------
    public function appendNum($bits, $num)
    {
        if ($bits == 0)
            return 0;

        $b = QRbitstream::newFromNum($bits, $num);

        if(is_null($b))
            return -1;

        $ret = $this->append($b);
        unset($b);

        return $ret;
    }

    //----------------------------------------------------------------------
    public function appendBytes($size, $data)
    {
        if ($size == 0)
            return 0;

        $b = QRbitstream::newFromBytes($size, $data);

        if(is_null($b))
            return -1;

        $ret = $this->append($b);
        unset($b);

        return $ret;
    }

    //----------------------------------------------------------------------
    public function toByte()
    {

        $size = $this->size();

        if($size == 0) {
            return array();
        }

        $data = array_fill(0, (int)(($size + 7) / 8), 0);
        $bytes = (int)($size / 8);

        $p = 0;

        for($i=0; $i<$bytes; $i++) {
            $v = 0;
            for($j=0; $j<8; $j++) {
                $v = $v << 1;
                $v |= $this->data[$p];
                $p++;
            }
            $data[$i] = $v;
        }

        if($size & 7) {
            $v = 0;
            for($j=0; $j<($size & 7); $j++) {
                $v = $v << 1;
                $v |= $this->data[$p];
                $p++;
            }
            $data[$bytes] = $v;
        }

        return $data;
    }

}