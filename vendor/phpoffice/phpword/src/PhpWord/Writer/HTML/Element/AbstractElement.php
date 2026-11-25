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

namespace PhpOffice\PhpWord\Writer\HTML\Element;

use PhpOffice\PhpWord\Element\AbstractElement as Element;
use PhpOffice\PhpWord\Writer\HTML;

/**
 * Abstract HTML element writer.
 *
 * @since 0.11.0
 */
abstract class AbstractElement
{
    /**
     * Parent writer.
     *
     * @var HTML
     */
    protected $parentWriter;

    /**
     * Element.
     *
     * @var Element
     */
    protected $element;

    /**
     * Without paragraph.
     *
     * @var bool
     */
    protected $withoutP = false;

    /**
     * Write element.
     */
    abstract public function write();

    /**
     * Create new instance.
     *
     * @param bool $withoutP
     */
    public function __construct(HTML $parentWriter, Element $element, $withoutP = false)
    {
        $this->parentWriter = $parentWriter;
        $this->element = $element;
        $this->withoutP = $withoutP;
    }

    /**
     * Set without paragraph.
     *
     * @param bool $value
     */
    public function setWithoutP($value): void
    {
        $this->withoutP = $value;
    }
}
