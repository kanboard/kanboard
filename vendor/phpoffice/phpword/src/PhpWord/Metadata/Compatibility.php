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

namespace PhpOffice\PhpWord\Metadata;

/**
 * Compatibility setting class.
 *
 * @since 0.12.0
 * @see  http://www.datypic.com/sc/ooxml/t-w_CT_Compat.html
 */
class Compatibility
{
    /**
     * OOXML version.
     *
     * 12 = 2007
     * 14 = 2010
     * 15 = 2013
     *
     * @var int
     *
     * @see  http://msdn.microsoft.com/en-us/library/dd909048%28v=office.12%29.aspx
     */
    private $ooxmlVersion = 12;

    /**
     * Get OOXML version.
     *
     * @return int
     */
    public function getOoxmlVersion()
    {
        return $this->ooxmlVersion;
    }

    /**
     * Set OOXML version.
     *
     * @param int $value
     *
     * @return self
     */
    public function setOoxmlVersion($value)
    {
        $this->ooxmlVersion = $value;

        return $this;
    }
}
