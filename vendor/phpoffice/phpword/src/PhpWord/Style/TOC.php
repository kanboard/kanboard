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
 * TOC style.
 */
class TOC extends Tab
{
    /**
     * Indent.
     *
     * @var float|int (twip)
     */
    private $indent = 200;

    /**
     * Create a new TOC Style.
     */
    public function __construct()
    {
        parent::__construct(self::TAB_STOP_RIGHT, 9062, self::TAB_LEADER_DOT);
    }

    /**
     * Get Tab Position.
     *
     * @return float|int
     */
    public function getTabPos()
    {
        return $this->getPosition();
    }

    /**
     * Set Tab Position.
     *
     * @param float|int $value
     *
     * @return self
     */
    public function setTabPos($value)
    {
        return $this->setPosition($value);
    }

    /**
     * Get Tab Leader.
     *
     * @return string
     */
    public function getTabLeader()
    {
        return $this->getLeader();
    }

    /**
     * Set Tab Leader.
     *
     * @param string $value
     *
     * @return self
     */
    public function setTabLeader($value = self::TAB_LEADER_DOT)
    {
        return $this->setLeader($value);
    }

    /**
     * Get Indent.
     *
     * @return float|int
     */
    public function getIndent()
    {
        return $this->indent;
    }

    /**
     * Set Indent.
     *
     * @param float|int $value
     *
     * @return self
     */
    public function setIndent($value)
    {
        $this->indent = $this->setNumericVal($value, $this->indent);

        return $this;
    }
}
