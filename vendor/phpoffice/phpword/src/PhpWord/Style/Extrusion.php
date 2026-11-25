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
 * 3D extrusion style.
 *
 * @see  http://www.schemacentral.com/sc/ooxml/t-o_CT_Extrusion.html
 * @since 0.12.0
 */
class Extrusion extends AbstractStyle
{
    /**
     * Type constants.
     *
     * @const string
     */
    const EXTRUSION_PARALLEL = 'parallel';
    const EXTRUSION_PERSPECTIVE = 'perspective';

    /**
     * Type: parallel|perspective.
     *
     * @var string
     */
    private $type;

    /**
     * Color.
     *
     * @var string
     */
    private $color;

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
     * Get type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set pattern.
     *
     * @param string $value
     *
     * @return self
     */
    public function setType($value = null)
    {
        $enum = [self::EXTRUSION_PARALLEL, self::EXTRUSION_PERSPECTIVE];
        $this->type = $this->setEnumVal($value, $enum, null);

        return $this;
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
}
