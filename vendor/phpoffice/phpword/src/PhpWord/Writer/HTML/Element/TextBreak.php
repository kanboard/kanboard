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

/**
 * TextBreak element HTML writer.
 *
 * @since 0.10.0
 */
class TextBreak extends AbstractElement
{
    /**
     * Write text break.
     *
     * @return string
     */
    public function write()
    {
        if ($this->withoutP) {
            $content = '<br />' . PHP_EOL;
        } else {
            $content = '<p>&nbsp;</p>' . PHP_EOL;
        }

        return $content;
    }
}
