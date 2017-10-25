<?php
/*
 * PHP QR Code encoder
 *
 * Toolset, handy and debug utilites.
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

class QRtools {

    //----------------------------------------------------------------------
    public static function binarize($frame)
    {
        $len = count($frame);
        foreach ($frame as &$frameLine) {

            for($i=0; $i<$len; $i++) {
                $frameLine[$i] = (ord($frameLine[$i])&1)?'1':'0';
            }
        }

        return $frame;
    }

    //----------------------------------------------------------------------
    public static function tcpdfBarcodeArray($code, $mode = 'QR,L', $tcPdfVersion = '4.5.037')
    {
        $barcode_array = array();

        if (!is_array($mode))
            $mode = explode(',', $mode);

        $eccLevel = 'L';

        if (count($mode) > 1) {
            $eccLevel = $mode[1];
        }

        $qrTab = QRcode::text($code, false, $eccLevel);
        $size = count($qrTab);

        $barcode_array['num_rows'] = $size;
        $barcode_array['num_cols'] = $size;
        $barcode_array['bcode'] = array();

        foreach ($qrTab as $line) {
            $arrAdd = array();
            foreach(str_split($line) as $char)
                $arrAdd[] = ($char=='1')?1:0;
            $barcode_array['bcode'][] = $arrAdd;
        }

        return $barcode_array;
    }

    //----------------------------------------------------------------------
    public static function clearCache()
    {
        self::$frames = array();
    }

    //----------------------------------------------------------------------
    public static function buildCache()
    {
        QRtools::markTime('before_build_cache');

        $mask = new QRmask();
        for ($a=1; $a <= Constants::QRSPEC_VERSION_MAX; $a++) {
            $frame = QRspec::newFrame($a);
            if (Constants::QR_IMAGE) {
                $fileName = Constants::QR_CACHE_DIR.'frame_'.$a.'.png';
                QRimage::png(self::binarize($frame), $fileName, 1, 0);
            }

            $width = count($frame);
            $bitMask = array_fill(0, $width, array_fill(0, $width, 0));
            for ($maskNo=0; $maskNo<8; $maskNo++)
                $mask->makeMaskNo($maskNo, $width, $frame, $bitMask, true);
        }

        QRtools::markTime('after_build_cache');
    }

    //----------------------------------------------------------------------
    public static function log($outfile, $err)
    {
        if (Constants::QR_LOG_DIR !== false) {
            if ($err != '') {
                if ($outfile !== false) {
                    file_put_contents(Constants::QR_LOG_DIR.basename($outfile).'-errors.txt', date('Y-m-d H:i:s').': '.$err, FILE_APPEND);
                } else {
                    file_put_contents(Constants::QR_LOG_DIR.'errors.txt', date('Y-m-d H:i:s').': '.$err, FILE_APPEND);
                }
            }
        }
    }

    //----------------------------------------------------------------------
    public static function dumpMask($frame)
    {
        $width = count($frame);
        for($y=0;$y<$width;$y++) {
            for($x=0;$x<$width;$x++) {
                echo ord($frame[$y][$x]).',';
            }
        }
    }

    //----------------------------------------------------------------------
    public static function markTime($markerId)
    {
        list($usec, $sec) = explode(" ", microtime());
        $time = ((float)$usec + (float)$sec);

        if (!isset($GLOBALS['qr_time_bench']))
            $GLOBALS['qr_time_bench'] = array();

        $GLOBALS['qr_time_bench'][$markerId] = $time;
    }

    //----------------------------------------------------------------------
    public static function timeBenchmark()
    {
        self::markTime('finish');

        $lastTime = 0;
        $startTime = 0;
        $p = 0;

        echo '<table cellpadding="3" cellspacing="1">
                <thead><tr style="border-bottom:1px solid silver"><td colspan="2" style="text-align:center">BENCHMARK</td></tr></thead>
                <tbody>';

        foreach($GLOBALS['qr_time_bench'] as $markerId=>$thisTime) {
            if ($p > 0) {
                echo '<tr><th style="text-align:right">till '.$markerId.': </th><td>'.number_format($thisTime-$lastTime, 6).'s</td></tr>';
            } else {
                $startTime = $thisTime;
            }

            $p++;
            $lastTime = $thisTime;
        }

        echo '</tbody><tfoot>
            <tr style="border-top:2px solid black"><th style="text-align:right">TOTAL: </th><td>'.number_format($lastTime-$startTime, 6).'s</td></tr>
        </tfoot>
        </table>';
    }

}

QRtools::markTime('start');
