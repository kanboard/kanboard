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

namespace PhpOffice\PhpWord;

use PhpOffice\PhpWord\Style\AbstractStyle;
use PhpOffice\PhpWord\Style\Font;
use PhpOffice\PhpWord\Style\Numbering;
use PhpOffice\PhpWord\Style\Paragraph;
use PhpOffice\PhpWord\Style\Table;

/**
 * Style collection.
 */
class Style
{
    /**
     * Style register.
     *
     * @var array
     */
    private static $styles = [];

    /**
     * Add paragraph style.
     *
     * @param string $styleName
     * @param AbstractStyle|array $styles
     *
     * @return Paragraph
     */
    public static function addParagraphStyle($styleName, $styles)
    {
        return self::setStyleValues($styleName, new Paragraph(), $styles);
    }

    /**
     * Add font style.
     *
     * @param string $styleName
     * @param AbstractStyle|array $fontStyle
     * @param AbstractStyle|array $paragraphStyle
     *
     * @return Font
     */
    public static function addFontStyle($styleName, $fontStyle, $paragraphStyle = null)
    {
        return self::setStyleValues($styleName, new Font('text', $paragraphStyle), $fontStyle);
    }

    /**
     * Add link style.
     *
     * @param string $styleName
     * @param AbstractStyle|array $styles
     *
     * @return Font
     */
    public static function addLinkStyle($styleName, $styles)
    {
        return self::setStyleValues($styleName, new Font('link'), $styles);
    }

    /**
     * Add numbering style.
     *
     * @param string $styleName
     * @param AbstractStyle|array $styleValues
     *
     * @return Numbering
     *
     * @since 0.10.0
     */
    public static function addNumberingStyle($styleName, $styleValues)
    {
        return self::setStyleValues($styleName, new Numbering(), $styleValues);
    }

    /**
     * Add title style.
     *
     * @param null|int $depth Provide null to set title font
     * @param AbstractStyle|array $fontStyle
     * @param AbstractStyle|array $paragraphStyle
     *
     * @return Font
     */
    public static function addTitleStyle($depth, $fontStyle, $paragraphStyle = null)
    {
        if (empty($depth)) {
            $styleName = 'Title';
        } else {
            $styleName = "Heading_{$depth}";
        }

        return self::setStyleValues($styleName, new Font('title', $paragraphStyle), $fontStyle);
    }

    /**
     * Add table style.
     *
     * @param string $styleName
     * @param array $styleTable
     * @param null|array $styleFirstRow
     *
     * @return Table
     */
    public static function addTableStyle($styleName, $styleTable, $styleFirstRow = null)
    {
        return self::setStyleValues($styleName, new Table($styleTable, $styleFirstRow), null);
    }

    /**
     * Count styles.
     *
     * @return int
     *
     * @since 0.10.0
     */
    public static function countStyles()
    {
        return count(self::$styles);
    }

    /**
     * Reset styles.
     *
     * @since 0.10.0
     */
    public static function resetStyles(): void
    {
        self::$styles = [];
    }

    /**
     * Set default paragraph style.
     *
     * @param AbstractStyle|array $styles Paragraph style definition
     *
     * @return Paragraph
     */
    public static function setDefaultParagraphStyle($styles)
    {
        return self::addParagraphStyle('Normal', $styles);
    }

    /**
     * Get all styles.
     *
     * @return AbstractStyle[]
     */
    public static function getStyles()
    {
        return self::$styles;
    }

    /**
     * Get style by name.
     *
     * @param string $styleName
     *
     * @return ?AbstractStyle Paragraph|Font|Table|Numbering
     */
    public static function getStyle($styleName)
    {
        if (isset(self::$styles[$styleName])) {
            return self::$styles[$styleName];
        }

        return null;
    }

    /**
     * Set style values and put it to static style collection.
     *
     * The $styleValues could be an array or object
     *
     * @param string $name
     * @param AbstractStyle $style
     * @param AbstractStyle|array $value
     *
     * @return AbstractStyle
     */
    private static function setStyleValues($name, $style, $value = null)
    {
        if (!isset(self::$styles[$name])) {
            if ($value !== null) {
                if (is_array($value)) {
                    $style->setStyleByArray($value);
                } elseif ($value instanceof AbstractStyle) {
                    if (get_class($style) == get_class($value)) {
                        $style = $value;
                    }
                }
            }
            $style->setStyleName($name);
            $style->setIndex(self::countStyles() + 1); // One based index
            self::$styles[$name] = $style;
        }

        return self::getStyle($name);
    }
}
