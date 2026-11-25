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

namespace PhpOffice\PhpWord\Style;

/**
 * Shadow style.
 *
 * @see  http://www.schemacentral.com/sc/ooxml/t-v_CT_Shadow.html
 * @since 0.12.0
 */
class Shadow extends AbstractStyle
{
    /**
     * Color.
     *
     * @var string
     */
    private $color;

    /**
     * Offset; Format: 3pt,3pt.
     *
     * @var string
     */
    private $offset;

    /**
     * Create a new instance.
     *
     * @param array $style
     */
    public function __construct($style = [])
    {
        $this->setStyleByArray($style);
    }

    /**
     * Get color.
     *
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set color.
     *
     * @param string $value
     *
     * @return self
     */
    public function setColor($value = null)
    {
        $this->color = $value;

        return $this;
    }

    /**
     * Get offset.
     *
     * @return string
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * Set offset.
     *
     * @param string $value
     *
     * @return self
     */
    public function setOffset($value = null)
    {
        $this->offset = $value;

        return $this;
    }
}
