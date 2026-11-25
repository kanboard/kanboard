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

use PhpOffice\PhpWord\Style;

/**
 * List item style.
 *
 * Before version 0.10.0, numbering style is defined statically with $listType.
 * After version 0.10.0, numbering style is defined by using Numbering and
 * recorded by $numStyle. $listStyle is maintained for backward compatility
 */
class ListItem extends AbstractStyle
{
    const TYPE_SQUARE_FILLED = 1;
    const TYPE_BULLET_FILLED = 3; // default
    const TYPE_BULLET_EMPTY = 5;
    const TYPE_NUMBER = 7;
    const TYPE_NUMBER_NESTED = 8;
    const TYPE_ALPHANUM = 9;

    /**
     * Legacy list type.
     *
     * @var int
     */
    private $listType;

    /**
     * Numbering style name.
     *
     * @var string
     *
     * @since 0.10.0
     */
    private $numStyle;

    /**
     * Numbering definition instance ID.
     *
     * @var int
     *
     * @since 0.10.0
     */
    private $numId;

    /**
     * Create new instance.
     *
     * @param string $numStyle
     */
    public function __construct($numStyle = null)
    {
        if ($numStyle !== null) {
            $this->setNumStyle($numStyle);
        } else {
            $this->setListType();
        }
    }

    /**
     * Get List Type.
     *
     * @return int
     */
    public function getListType()
    {
        return $this->listType;
    }

    /**
     * Set legacy list type for version < 0.10.0.
     *
     * @param int $value
     *
     * @return self
     */
    public function setListType($value = self::TYPE_BULLET_FILLED)
    {
        $enum = [
            self::TYPE_SQUARE_FILLED, self::TYPE_BULLET_FILLED,
            self::TYPE_BULLET_EMPTY, self::TYPE_NUMBER,
            self::TYPE_NUMBER_NESTED, self::TYPE_ALPHANUM,
        ];
        $this->listType = $this->setEnumVal($value, $enum, $this->listType);
        $this->getListTypeStyle();

        return $this;
    }

    /**
     * Get numbering style name.
     *
     * @return string
     */
    public function getNumStyle()
    {
        return $this->numStyle;
    }

    /**
     * Set numbering style name.
     *
     * @param string $value
     *
     * @return self
     */
    public function setNumStyle($value)
    {
        $this->numStyle = $value;
        $numStyleObject = Style::getStyle($this->numStyle);
        if ($numStyleObject instanceof Numbering) {
            $this->numId = $numStyleObject->getIndex();
            $numStyleObject->setNumId($this->numId);
        }

        return $this;
    }

    /**
     * Get numbering Id.
     *
     * @return int
     */
    public function getNumId()
    {
        return $this->numId;
    }

    /**
     * Set numbering Id. Same numId means same list.
     *
     * @param mixed $numInt
     */
    public function setNumId($numInt): void
    {
        $this->numId = $numInt;
        $this->getListTypeStyle();
    }

    /**
     * Get legacy numbering definition.
     *
     * @return array
     *
     * @since 0.10.0
     */
    private function getListTypeStyle()
    {
        // Check if legacy style already registered in global Style collection
        $numStyle = 'PHPWordListType' . $this->listType;

        if ($this->numId) {
            $numStyle .= 'NumId' . $this->numId;
        }

        if (Style::getStyle($numStyle) !== null) {
            $this->setNumStyle($numStyle);

            return;
        }

        // Property mapping for numbering level information
        $properties = ['start', 'format', 'text', 'alignment', 'tabPos', 'left', 'hanging', 'font', 'hint'];

        // Legacy level information
        $listTypeStyles = [
            self::TYPE_SQUARE_FILLED => [
                'type' => 'hybridMultilevel',
                'levels' => [
                    0 => '1, bullet, , left, 720, 720, 360, Wingdings, default',
                    1 => '1, bullet, o, left, 1440, 1440, 360, Courier New, default',
                    2 => '1, bullet, , left, 2160, 2160, 360, Wingdings, default',
                    3 => '1, bullet, , left, 2880, 2880, 360, Symbol, default',
                    4 => '1, bullet, o, left, 3600, 3600, 360, Courier New, default',
                    5 => '1, bullet, , left, 4320, 4320, 360, Wingdings, default',
                    6 => '1, bullet, , left, 5040, 5040, 360, Symbol, default',
                    7 => '1, bullet, o, left, 5760, 5760, 360, Courier New, default',
                    8 => '1, bullet, , left, 6480, 6480, 360, Wingdings, default',
                ],
            ],
            self::TYPE_BULLET_FILLED => [
                'type' => 'hybridMultilevel',
                'levels' => [
                    0 => '1, bullet, , left, 720, 720, 360, Symbol, default',
                    1 => '1, bullet, o, left, 1440, 1440, 360, Courier New, default',
                    2 => '1, bullet, , left, 2160, 2160, 360, Wingdings, default',
                    3 => '1, bullet, , left, 2880, 2880, 360, Symbol, default',
                    4 => '1, bullet, o, left, 3600, 3600, 360, Courier New, default',
                    5 => '1, bullet, , left, 4320, 4320, 360, Wingdings, default',
                    6 => '1, bullet, , left, 5040, 5040, 360, Symbol, default',
                    7 => '1, bullet, o, left, 5760, 5760, 360, Courier New, default',
                    8 => '1, bullet, , left, 6480, 6480, 360, Wingdings, default',
                ],
            ],
            self::TYPE_BULLET_EMPTY => [
                'type' => 'hybridMultilevel',
                'levels' => [
                    0 => '1, bullet, o, left, 720, 720, 360, Courier New, default',
                    1 => '1, bullet, o, left, 1440, 1440, 360, Courier New, default',
                    2 => '1, bullet, , left, 2160, 2160, 360, Wingdings, default',
                    3 => '1, bullet, , left, 2880, 2880, 360, Symbol, default',
                    4 => '1, bullet, o, left, 3600, 3600, 360, Courier New, default',
                    5 => '1, bullet, , left, 4320, 4320, 360, Wingdings, default',
                    6 => '1, bullet, , left, 5040, 5040, 360, Symbol, default',
                    7 => '1, bullet, o, left, 5760, 5760, 360, Courier New, default',
                    8 => '1, bullet, , left, 6480, 6480, 360, Wingdings, default',
                ],
            ],
            self::TYPE_NUMBER => [
                'type' => 'hybridMultilevel',
                'levels' => [
                    0 => '1, decimal, %1., left, 720, 720, 360, , default',
                    1 => '1, bullet, o, left, 1440, 1440, 360, Courier New, default',
                    2 => '1, bullet, , left, 2160, 2160, 360, Wingdings, default',
                    3 => '1, bullet, , left, 2880, 2880, 360, Symbol, default',
                    4 => '1, bullet, o, left, 3600, 3600, 360, Courier New, default',
                    5 => '1, bullet, , left, 4320, 4320, 360, Wingdings, default',
                    6 => '1, bullet, , left, 5040, 5040, 360, Symbol, default',
                    7 => '1, bullet, o, left, 5760, 5760, 360, Courier New, default',
                    8 => '1, bullet, , left, 6480, 6480, 360, Wingdings, default',
                ],
            ],
            self::TYPE_NUMBER_NESTED => [
                'type' => 'multilevel',
                'levels' => [
                    0 => '1, decimal, %1., left, 360, 360, 360, , ',
                    1 => '1, decimal, %1.%2., left, 792, 792, 432, , ',
                    2 => '1, decimal, %1.%2.%3., left, 1224, 1224, 504, , ',
                    3 => '1, decimal, %1.%2.%3.%4., left, 1800, 1728, 648, , ',
                    4 => '1, decimal, %1.%2.%3.%4.%5., left, 2520, 2232, 792, , ',
                    5 => '1, decimal, %1.%2.%3.%4.%5.%6., left, 2880, 2736, 936, , ',
                    6 => '1, decimal, %1.%2.%3.%4.%5.%6.%7., left, 3600, 3240, 1080, , ',
                    7 => '1, decimal, %1.%2.%3.%4.%5.%6.%7.%8., left, 3960, 3744, 1224, , ',
                    8 => '1, decimal, %1.%2.%3.%4.%5.%6.%7.%8.%9., left, 4680, 4320, 1440, , ',
                ],
            ],
            self::TYPE_ALPHANUM => [
                'type' => 'multilevel',
                'levels' => [
                    0 => '1, decimal, %1., left, 720, 720, 360, , ',
                    1 => '1, lowerLetter, %2., left, 1440, 1440, 360, , ',
                    2 => '1, lowerRoman, %3., right, 2160, 2160, 180, , ',
                    3 => '1, decimal, %4., left, 2880, 2880, 360, , ',
                    4 => '1, lowerLetter, %5., left, 3600, 3600, 360, , ',
                    5 => '1, lowerRoman, %6., right, 4320, 4320, 180, , ',
                    6 => '1, decimal, %7., left, 5040, 5040, 360, , ',
                    7 => '1, lowerLetter, %8., left, 5760, 5760, 360, , ',
                    8 => '1, lowerRoman, %9., right, 6480, 6480, 180, , ',
                ],
            ],
        ];

        // Populate style and register to global Style register
        $style = $listTypeStyles[$this->listType];
        $numProperties = count($properties);
        foreach ($style['levels'] as $key => $value) {
            $level = [];
            $levelProperties = explode(', ', $value);
            $level['level'] = $key;
            for ($i = 0; $i < $numProperties; ++$i) {
                $property = $properties[$i];
                $level[$property] = $levelProperties[$i];
            }
            $style['levels'][$key] = $level;
        }
        Style::addNumberingStyle($numStyle, $style);
        $this->setNumStyle($numStyle);
    }
}
