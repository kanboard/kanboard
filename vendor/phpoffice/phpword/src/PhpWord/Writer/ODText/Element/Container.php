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

namespace PhpOffice\PhpWord\Writer\ODText\Element;

use PhpOffice\PhpWord\Writer\Word2007\Element\Container as Word2007Container;

/**
 * Container element writer (section, textrun, header, footnote, cell, etc.).
 *
 * @since 0.11.0
 */
class Container extends Word2007Container
{
    /**
     * Namespace; Can't use __NAMESPACE__ in inherited class (ODText).
     *
     * @var string
     */
    protected $namespace = 'PhpOffice\\PhpWord\\Writer\\ODText\\Element';

    /**
     * @var array<string>
     */
    protected $containerWithoutP = ['TextRun', 'Footnote', 'Endnote'];
}
