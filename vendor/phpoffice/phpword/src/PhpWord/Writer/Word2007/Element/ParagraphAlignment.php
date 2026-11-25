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

namespace PhpOffice\PhpWord\Writer\Word2007\Element;

/**
 * @since 0.13.0
 */
class ParagraphAlignment
{
    private $name = 'w:jc';

    private $attributes = [];

    /**
     * @since 0.13.0
     *
     * @param string $value Any value provided by Jc simple type
     *
     * @see \PhpOffice\PhpWord\SimpleType\Jc For the allowed values of $value parameter.
     */
    final public function __construct($value)
    {
        $this->attributes['w:val'] = $value;
    }

    /**
     * @since 0.13.0
     *
     * @return string
     */
    final public function getName()
    {
        return $this->name;
    }

    /**
     * @since 0.13.0
     *
     * @return string[]
     */
    final public function getAttributes()
    {
        return $this->attributes;
    }
}
