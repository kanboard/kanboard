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
 * Table row style.
 *
 * @since 0.8.0
 */
class Row extends AbstractStyle
{
    /**
     * Repeat table row on every new page.
     *
     * @var bool
     */
    private $tblHeader = false;

    /**
     * Table row cannot break across pages.
     *
     * @var bool
     */
    private $cantSplit = false;

    /**
     * Table row exact height.
     *
     * @var bool
     */
    private $exactHeight = false;

    /**
     * Create a new row style.
     */
    public function __construct()
    {
    }

    /**
     * Is tblHeader.
     *
     * @return bool
     */
    public function isTblHeader()
    {
        return $this->tblHeader;
    }

    /**
     * Is tblHeader.
     *
     * @param bool $value
     *
     * @return self
     */
    public function setTblHeader($value = true)
    {
        $this->tblHeader = $this->setBoolVal($value, $this->tblHeader);

        return $this;
    }

    /**
     * Is cantSplit.
     *
     * @return bool
     */
    public function isCantSplit()
    {
        return $this->cantSplit;
    }

    /**
     * Is cantSplit.
     *
     * @param bool $value
     *
     * @return self
     */
    public function setCantSplit($value = true)
    {
        $this->cantSplit = $this->setBoolVal($value, $this->cantSplit);

        return $this;
    }

    /**
     * Is exactHeight.
     *
     * @return bool
     */
    public function isExactHeight()
    {
        return $this->exactHeight;
    }

    /**
     * Set exactHeight.
     *
     * @param bool $value
     *
     * @return self
     */
    public function setExactHeight($value = true)
    {
        $this->exactHeight = $this->setBoolVal($value, $this->exactHeight);

        return $this;
    }
}
