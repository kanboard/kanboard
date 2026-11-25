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

namespace PhpOffice\PhpWord\Element;

/**
 * Endnote element.
 *
 * @since 0.10.0
 */
class Endnote extends Footnote
{
    /**
     * @var string Container type
     */
    protected $container = 'Endnote';

    /**
     * Create new instance.
     *
     * @param array|\PhpOffice\PhpWord\Style\Paragraph|string $paragraphStyle
     */
    public function __construct($paragraphStyle = null)
    {
        parent::__construct($paragraphStyle);
    }
}
