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

use PhpOffice\PhpWord\Style\Cell as CellStyle;

/**
 * Table cell element.
 */
class Cell extends AbstractContainer
{
    /**
     * @var string Container type
     */
    protected $container = 'Cell';

    /**
     * Cell width.
     *
     * @var ?int
     */
    private $width;

    /**
     * Cell style.
     *
     * @var ?CellStyle
     */
    private $style;

    /**
     * Create new instance.
     *
     * @param null|int $width
     * @param array|CellStyle $style
     */
    public function __construct($width = null, $style = null)
    {
        $this->width = $width;
        $this->style = $this->setNewStyle(new CellStyle(), $style, true);
    }

    /**
     * Get cell style.
     *
     * @return ?CellStyle
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * Get cell width.
     *
     * @return ?int
     */
    public function getWidth()
    {
        return $this->width;
    }
}
