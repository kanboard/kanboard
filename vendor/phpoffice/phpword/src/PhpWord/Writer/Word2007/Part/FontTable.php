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

namespace PhpOffice\PhpWord\Writer\Word2007\Part;

/**
 * Word2007 font table writer: word/fontTable.xml.
 *
 * @todo Generate content dynamically
 *
 * @since 0.10.0
 */
class FontTable extends AbstractPart
{
    /**
     * Write fontTable.xml.
     *
     * @return string
     */
    public function write()
    {
        $str = '';
        $str .= '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        $str .= '<w:fonts ' .
            'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" ' .
            'xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">';

        $str .= '<w:font w:name="Times New Roman">';
        $str .= '<w:panose1 w:val="02020603050405020304" />';
        $str .= '<w:charset w:val="00" />';
        $str .= '<w:family w:val="roman" />';
        $str .= '<w:pitch w:val="variable" />';
        $str .= '<w:sig w:usb0="E0002AFF" w:usb1="C0007841" w:usb2="00000009" w:usb3="00000000" ' .
            'w:csb0="000001FF" w:csb1="00000000" />';
        $str .= '</w:font>';

        $str .= '<w:font w:name="Courier New">';
        $str .= '<w:panose1 w:val="02070309020205020404" />';
        $str .= '<w:charset w:val="00" />';
        $str .= '<w:family w:val="modern" />';
        $str .= '<w:pitch w:val="fixed" />';
        $str .= '<w:sig w:usb0="E0002AFF" w:usb1="C0007843" w:usb2="00000009" w:usb3="00000000" ' .
            'w:csb0="000001FF" w:csb1="00000000" />';
        $str .= '</w:font>';

        $str .= '<w:font w:name="Wingdings">';
        $str .= '<w:panose1 w:val="05000000000000000000" />';
        $str .= '<w:charset w:val="02" />';
        $str .= '<w:family w:val="auto" />';
        $str .= '<w:pitch w:val="variable" />';
        $str .= '<w:sig w:usb0="00000000" w:usb1="10000000" w:usb2="00000000" w:usb3="00000000" ' .
            'w:csb0="80000000" w:csb1="00000000" />';
        $str .= '</w:font>';

        $str .= '<w:font w:name="Symbol">';
        $str .= '<w:panose1 w:val="05050102010706020507" />';
        $str .= '<w:charset w:val="02" />';
        $str .= '<w:family w:val="roman" />';
        $str .= '<w:pitch w:val="variable" />';
        $str .= '<w:sig w:usb0="00000000" w:usb1="10000000" w:usb2="00000000" w:usb3="00000000" ' .
            'w:csb0="80000000" w:csb1="00000000" />';
        $str .= '</w:font>';

        $str .= '<w:font w:name="Arial">';
        $str .= '<w:panose1 w:val="020B0604020202020204" />';
        $str .= '<w:charset w:val="00" />';
        $str .= '<w:family w:val="swiss" />';
        $str .= '<w:pitch w:val="variable" />';
        $str .= '<w:sig w:usb0="E0002AFF" w:usb1="C0007843" w:usb2="00000009" w:usb3="00000000" ' .
            'w:csb0="000001FF" w:csb1="00000000" />';
        $str .= '</w:font>';

        $str .= '<w:font w:name="Cambria">';
        $str .= '<w:panose1 w:val="02040503050406030204" />';
        $str .= '<w:charset w:val="00" />';
        $str .= '<w:family w:val="roman" />';
        $str .= '<w:pitch w:val="variable" />';
        $str .= '<w:sig w:usb0="A00002EF" w:usb1="4000004B" w:usb2="00000000" w:usb3="00000000" ' .
            'w:csb0="0000019F" w:csb1="00000000" />';
        $str .= '</w:font>';

        $str .= '<w:font w:name="Calibri">';
        $str .= '<w:panose1 w:val="020F0502020204030204" />';
        $str .= '<w:charset w:val="00" />';
        $str .= '<w:family w:val="swiss" />';
        $str .= '<w:pitch w:val="variable" />';
        $str .= '<w:sig w:usb0="E10002FF" w:usb1="4000ACFF" w:usb2="00000009" w:usb3="00000000" ' .
            'w:csb0="0000019F" w:csb1="00000000" />';
        $str .= '</w:font>';

        $str .= '<w:font w:name="Garamond">';
        $str .= '<w:panose1 w:val="02020404030301010803" />';
        $str .= '<w:charset w:val="00" />';
        $str .= '<w:family w:val="roman" />';
        $str .= '<w:pitch w:val="variable" />';
        $str .= '<w:sig w:usb0="00000287" w:usb1="00000002" w:usb2="00000000" w:usb3="00000000" ' .
            'w:csb0="0000009F" w:csb1="00000000" />';
        $str .= '</w:font>';

        $str .= '</w:fonts>';

        return $str;
    }
}
