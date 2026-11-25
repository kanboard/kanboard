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
 * Header element.
 */
class Header extends Footer
{
    /**
     * @var string Container type
     */
    protected $container = 'Header';

    /**
     * Add a Watermark Element.
     *
     * @param string $src
     * @param mixed $style
     *
     * @return Image
     */
    public function addWatermark($src, $style = null)
    {
        return $this->addImage($src, $style, true);
    }
}
