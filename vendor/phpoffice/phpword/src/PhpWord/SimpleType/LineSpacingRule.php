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
 * Line Spacing Rule.
 *
 * @since 0.14.0
 * @see http://www.datypic.com/sc/ooxml/t-w_ST_LineSpacingRule.html
 */
final class LineSpacingRule extends AbstractEnum
{
    /**
     * Automatically Determined Line Height.
     */
    const AUTO = 'auto';

    /**
     * Exact Line Height.
     */
    const EXACT = 'exact';

    /**
     * Minimum Line Height.
     */
    const AT_LEAST = 'atLeast';
}
