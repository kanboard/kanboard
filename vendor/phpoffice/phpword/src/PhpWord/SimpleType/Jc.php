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

namespace PhpOffice\PhpWord\SimpleType;

use PhpOffice\PhpWord\Shared\AbstractEnum;

/**
 * Horizontal Alignment Type.
 *
 * Introduced in 1st Edition of ECMA-376. Initially it was intended to align paragraphs and tables.
 * Since ISO/IEC-29500:2008 the type must not be used for table alignment.
 *
 * @since 0.13.0
 * @see JcTable For table alignment modes available since ISO/IEC-29500:2008.
 * @see  http://www.datypic.com/sc/ooxml/t-w_ST_Jc.html
 */
final class Jc extends AbstractEnum
{
    const START = 'start';
    const CENTER = 'center';
    const END = 'end';
    const BOTH = 'both';
    const MEDIUM_KASHIDA = 'mediumKashida';
    const DISTRIBUTE = 'distribute';
    const NUM_TAB = 'numTab';
    const HIGH_KASHIDA = 'highKashida';
    const LOW_KASHIDA = 'lowKashida';
    const THAI_DISTRIBUTE = 'thaiDistribute';

    /**
     * Kept for compatibility with 1st edition of ECMA-376 standard.
     * Microsoft Word 2007 and WPS Writer 2016 still rely on it.
     *
     * @deprecated 0.13.0 For documents based on ISO/IEC 29500:2008 and later use `START` instead.
     */
    const LEFT = 'left';
    /**
     * Kept for compatibility with 1st edition of ECMA-376 standard.
     * Microsoft Word 2007 and WPS Writer 2016 still rely on it.
     *
     * @deprecated 0.13.0 For documents based on ISO/IEC 29500:2008 and later use `END` instead.
     */
    const RIGHT = 'right';
    /**
     * Kept for compatibility with 1st edition of ECMA-376 standard.
     * Microsoft Word 2007 and WPS Writer 2016 still rely on it.
     *
     * @deprecated 0.13.0 For documents based on ISO/IEC 29500:2008 and later use `BOTH` instead.
     */
    const JUSTIFY = 'justify';
}
