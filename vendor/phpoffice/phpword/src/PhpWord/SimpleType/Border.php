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
 * Border Styles.
 *
 * @since 0.18.0
 * @see  http://www.datypic.com/sc/ooxml/t-w_ST_Border.html
 */
final class Border extends AbstractEnum
{
    const SINGLE = 'single'; //A single line
    const DASH_DOT_STROKED = 'dashDotStroked'; //A line with a series of alternating thin and thick strokes
    const DASHED = 'dashed'; //A dashed line
    const DASH_SMALL_GAP = 'dashSmallGap'; //A dashed line with small gaps
    const DOT_DASH = 'dotDash'; //A line with alternating dots and dashes
    const DOT_DOT_DASH = 'dotDotDash'; //A line with a repeating dot - dot - dash sequence
    const DOTTED = 'dotted'; //A dotted line
    const DOUBLE = 'double'; //A double line
    const DOUBLE_WAVE = 'doubleWave'; //A double wavy line
    const INSET = 'inset'; //An inset set of lines
    const NIL = 'nil'; //No border
    const NONE = 'none'; //No border
    const OUTSET = 'outset'; //An outset set of lines
    const THICK = 'thick'; //A single line
    const THICK_THIN_LARGE_GAP = 'thickThinLargeGap'; //A thick line contained within a thin line with a large-sized intermediate gap
    const THICK_THIN_MEDIUM_GAP = 'thickThinMediumGap'; //A thick line contained within a thin line with a medium-sized intermediate gap
    const THICK_THIN_SMALL_GAP = 'thickThinSmallGap'; //A thick line contained within a thin line with a small intermediate gap
    const THIN_THICK_LARGE_GAP = 'thinThickLargeGap'; //A thin line contained within a thick line with a large-sized intermediate gap
    const THIN_THICK_MEDIUM_GAP = 'thinThickMediumGap'; //A thick line contained within a thin line with a medium-sized intermediate gap
    const THIN_THICK_SMALL_GAP = 'thinThickSmallGap'; //A thick line contained within a thin line with a small intermediate gap
    const THIN_THICK_THINLARGE_GAP = 'thinThickThinLargeGap'; //A thin-thick-thin line with a large gap
    const THIN_THICK_THIN_MEDIUM_GAP = 'thinThickThinMediumGap'; //A thin-thick-thin line with a medium gap
    const THIN_THICK_THIN_SMALL_GAP = 'thinThickThinSmallGap'; //A thin-thick-thin line with a small gap
    const THREE_D_EMBOSS = 'threeDEmboss'; //A three-staged gradient line, getting darker towards the paragraph
    const THREE_D_ENGRAVE = 'threeDEngrave'; //A three-staged gradient like, getting darker away from the paragraph
    const TRIPLE = 'triple'; //A triple line
    const WAVE = 'wave'; //A wavy line
}
