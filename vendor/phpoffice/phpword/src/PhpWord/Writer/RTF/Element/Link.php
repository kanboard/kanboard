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

namespace PhpOffice\PhpWord\Writer\RTF\Element;

/**
 * Link element RTF writer.
 *
 * @since 0.11.0
 */
class Link extends AbstractElement
{
    /**
     * Write element.
     *
     * @return string
     */
    public function write()
    {
        if (!$this->element instanceof \PhpOffice\PhpWord\Element\Link) {
            return '';
        }

        $this->getStyles();

        $content = '';
        $content .= $this->writeOpening();
        $content .= '{\field {\*\fldinst {HYPERLINK "' . $this->element->getSource() . '"}}{\\fldrslt {';
        $content .= $this->writeFontStyle();
        $content .= $this->writeText($this->element->getText());
        $content .= '}}}';
        $content .= $this->writeClosing();

        return $content;
    }
}
