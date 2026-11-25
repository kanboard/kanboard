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

namespace PhpOffice\PhpWord\ComplexType;

use PhpOffice\PhpWord\SimpleType\TblWidth as TblWidthSimpleType;

/**
 * @see http://www.datypic.com/sc/ooxml/t-w_CT_TblWidth.html
 */
final class TblWidth
{
    /** @var string */
    private $type;

    /** @var int */
    private $value;

    /**
     * @param int $value If omitted, then its value shall be assumed to be 0
     * @param string $type If omitted, then its value shall be assumed to be dxa
     */
    public function __construct($value = 0, $type = TblWidthSimpleType::TWIP)
    {
        $this->value = $value;
        TblWidthSimpleType::validate($type);
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }
}
