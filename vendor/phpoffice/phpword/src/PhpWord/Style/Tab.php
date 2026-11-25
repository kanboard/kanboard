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
 * Tab style.
 */
class Tab extends AbstractStyle
{
    /**
     * Tab stop types.
     *
     * @const string
     */
    const TAB_STOP_CLEAR = 'clear';
    const TAB_STOP_LEFT = 'left';
    const TAB_STOP_CENTER = 'center';
    const TAB_STOP_RIGHT = 'right';
    const TAB_STOP_DECIMAL = 'decimal';
    const TAB_STOP_BAR = 'bar';
    const TAB_STOP_NUM = 'num';

    /**
     * Tab leader types.
     *
     * @const string
     */
    const TAB_LEADER_NONE = 'none';
    const TAB_LEADER_DOT = 'dot';
    const TAB_LEADER_HYPHEN = 'hyphen';
    const TAB_LEADER_UNDERSCORE = 'underscore';
    const TAB_LEADER_HEAVY = 'heavy';
    const TAB_LEADER_MIDDLEDOT = 'middleDot';

    /**
     * Tab stop type.
     *
     * @var string
     */
    private $type = self::TAB_STOP_CLEAR;

    /**
     * Tab leader character.
     *
     * @var string
     */
    private $leader = self::TAB_LEADER_NONE;

    /**
     * Tab stop position (twip).
     *
     * @var float|int
     */
    private $position = 0;

    /**
     * Create a new instance of Tab. Both $type and $leader
     * must conform to the values put forth in the schema. If they do not
     * they will be changed to default values.
     *
     * @param string $type Defaults to 'clear' if value is not possible
     * @param int $position Must be numeric; otherwise defaults to 0
     * @param string $leader Defaults to null if value is not possible
     */
    public function __construct($type = null, $position = 0, $leader = null)
    {
        $stopTypes = [
            self::TAB_STOP_CLEAR, self::TAB_STOP_LEFT, self::TAB_STOP_CENTER,
            self::TAB_STOP_RIGHT, self::TAB_STOP_DECIMAL, self::TAB_STOP_BAR, self::TAB_STOP_NUM,
        ];
        $leaderTypes = [
            self::TAB_LEADER_NONE, self::TAB_LEADER_DOT, self::TAB_LEADER_HYPHEN,
            self::TAB_LEADER_UNDERSCORE, self::TAB_LEADER_HEAVY, self::TAB_LEADER_MIDDLEDOT,
        ];

        $this->type = $this->setEnumVal($type, $stopTypes, $this->type);
        $this->position = $this->setNumericVal($position, $this->position);
        $this->leader = $this->setEnumVal($leader, $leaderTypes, $this->leader);
    }

    /**
     * Get stop type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set stop type.
     *
     * @param string $value
     *
     * @return self
     */
    public function setType($value)
    {
        $enum = [
            self::TAB_STOP_CLEAR, self::TAB_STOP_LEFT, self::TAB_STOP_CENTER,
            self::TAB_STOP_RIGHT, self::TAB_STOP_DECIMAL, self::TAB_STOP_BAR,
            self::TAB_STOP_NUM,
        ];
        $this->type = $this->setEnumVal($value, $enum, $this->type);

        return $this;
    }

    /**
     * Get leader.
     *
     * @return string
     */
    public function getLeader()
    {
        return $this->leader;
    }

    /**
     * Set leader.
     *
     * @param string $value
     *
     * @return self
     */
    public function setLeader($value)
    {
        $enum = [
            self::TAB_LEADER_NONE, self::TAB_LEADER_DOT, self::TAB_LEADER_HYPHEN,
            self::TAB_LEADER_UNDERSCORE, self::TAB_LEADER_HEAVY, self::TAB_LEADER_MIDDLEDOT,
        ];
        $this->leader = $this->setEnumVal($value, $enum, $this->leader);

        return $this;
    }

    /**
     * Get position.
     *
     * @return float|int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set position.
     *
     * @param float|int $value
     *
     * @return self
     */
    public function setPosition($value)
    {
        $this->position = $this->setNumericVal($value, $this->position);

        return $this;
    }
}
