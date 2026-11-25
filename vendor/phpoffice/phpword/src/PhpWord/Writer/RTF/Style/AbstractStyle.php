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

namespace PhpOffice\PhpWord\Writer\RTF\Style;

use PhpOffice\PhpWord\Style\AbstractStyle as StyleAbstract;
use PhpOffice\PhpWord\Writer\RTF;

/**
 * Abstract RTF style writer.
 *
 * @since 0.11.0
 */
abstract class AbstractStyle
{
    /**
     * Parent writer.
     *
     * @var RTF
     */
    private $parentWriter;

    /**
     * Style.
     *
     * @var null|array|StyleAbstract
     */
    private $style;

    /**
     * Write style.
     *
     * @return mixed
     */
    abstract public function write();

    /**
     * Create new instance.
     *
     * @param array|StyleAbstract $style
     */
    public function __construct($style = null)
    {
        $this->style = $style;
    }

    /**
     * Set parent writer.
     *
     * @param RTF $writer
     */
    public function setParentWriter($writer): void
    {
        $this->parentWriter = $writer;
    }

    /**
     * Get parent writer.
     *
     * @return RTF
     */
    public function getParentWriter()
    {
        return $this->parentWriter;
    }

    /**
     * Get style.
     *
     * @return null|array|string|StyleAbstract
     */
    public function getStyle()
    {
        if (!$this->style instanceof StyleAbstract && !is_array($this->style)) {
            return '';
        }

        return $this->style;
    }

    /**
     * Get value if ...
     *
     * @param null|bool $condition
     * @param string $value
     *
     * @return string
     */
    protected function getValueIf($condition, $value)
    {
        return $condition == true ? $value : '';
    }
}
