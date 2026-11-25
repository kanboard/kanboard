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

namespace PhpOffice\PhpWord\Element;

use PhpOffice\PhpWord\Style\Chart as ChartStyle;

/**
 * Chart element.
 *
 * @since 0.12.0
 */
class Chart extends AbstractElement
{
    /**
     * Is part of collection.
     *
     * @var bool
     */
    protected $collectionRelation = true;

    /**
     * Type.
     *
     * @var string
     */
    private $type = 'pie';

    /**
     * Series.
     *
     * @var array
     */
    private $series = [];

    /**
     * Chart style.
     *
     * @var ?ChartStyle
     */
    private $style;

    /**
     * Create new instance.
     *
     * @param string $type
     * @param array $categories
     * @param array $values
     * @param array $style
     * @param null|mixed $seriesName
     */
    public function __construct($type, $categories, $values, $style = null, $seriesName = null)
    {
        $this->setType($type);
        $this->addSeries($categories, $values, $seriesName);
        $this->style = $this->setNewStyle(new ChartStyle(), $style, true);
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
     * Set type.
     *
     * @param string $value
     */
    public function setType($value): void
    {
        $enum = ['pie', 'doughnut', 'line', 'bar', 'stacked_bar', 'percent_stacked_bar', 'column', 'stacked_column', 'percent_stacked_column', 'area', 'radar', 'scatter'];
        $this->type = $this->setEnumVal($value, $enum, 'pie');
    }

    /**
     * Add series.
     *
     * @param array $categories
     * @param array $values
     * @param null|mixed $name
     */
    public function addSeries($categories, $values, $name = null): void
    {
        $this->series[] = [
            'categories' => $categories,
            'values' => $values,
            'name' => $name,
        ];
    }

    /**
     * Get series.
     *
     * @return array
     */
    public function getSeries()
    {
        return $this->series;
    }

    /**
     * Get chart style.
     *
     * @return ?ChartStyle
     */
    public function getStyle()
    {
        return $this->style;
    }
}
