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

namespace PhpOffice\PhpWord\Writer\Word2007\Style;

use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\Shared\XMLWriter;

/**
 * Style writer.
 *
 * @since 0.10.0
 */
abstract class AbstractStyle
{
    /**
     * XML writer.
     *
     * @var XMLWriter
     */
    private $xmlWriter;

    /**
     * Style; set protected for a while.
     *
     * @var \PhpOffice\PhpWord\Style\AbstractStyle|string
     */
    protected $style;

    /**
     * Write style.
     */
    abstract public function write();

    /**
     * Create new instance.
     *
     * @param \PhpOffice\PhpWord\Style\AbstractStyle|string $style
     */
    public function __construct(XMLWriter $xmlWriter, $style = null)
    {
        $this->xmlWriter = $xmlWriter;
        $this->style = $style;
    }

    /**
     * Get XML Writer.
     *
     * @return XMLWriter
     */
    protected function getXmlWriter()
    {
        return $this->xmlWriter;
    }

    /**
     * Get Style.
     *
     * @return \PhpOffice\PhpWord\Style\AbstractStyle|string
     */
    protected function getStyle()
    {
        return $this->style;
    }

    /**
     * Convert twip value.
     *
     * @param float|int $value
     * @param int $default (int|float)
     *
     * @return float|int
     */
    protected function convertTwip($value, $default = 0)
    {
        $factors = [
            Settings::UNIT_CM => 567,
            Settings::UNIT_MM => 56.7,
            Settings::UNIT_INCH => 1440,
            Settings::UNIT_POINT => 20,
            Settings::UNIT_PICA => 240,
        ];
        $unit = Settings::getMeasurementUnit();
        $factor = 1;
        if (array_key_exists($unit, $factors) && $value != $default) {
            $factor = $factors[$unit];
        }

        return $value * $factor;
    }

    /**
     * Write child style.
     *
     * @param string $name
     * @param mixed $value
     */
    protected function writeChildStyle(XMLWriter $xmlWriter, $name, $value): void
    {
        if ($value !== null) {
            $class = 'PhpOffice\\PhpWord\\Writer\\Word2007\\Style\\' . $name;

            /** @var AbstractStyle $writer */
            $writer = new $class($xmlWriter, $value);
            $writer->write();
        }
    }

    /**
     * Writes boolean as 0 or 1.
     *
     * @param bool $value
     *
     * @return null|string
     */
    protected function writeOnOf($value = null)
    {
        if ($value === null) {
            return null;
        }

        return $value ? '1' : '0';
    }

    /**
     * Assemble style array into style string.
     *
     * @param array $styles
     *
     * @return string
     */
    protected function assembleStyle($styles = [])
    {
        $style = '';
        foreach ($styles as $key => $value) {
            if (null !== $value && $value != '') {
                $style .= "{$key}:{$value}; ";
            }
        }

        return trim($style);
    }
}
