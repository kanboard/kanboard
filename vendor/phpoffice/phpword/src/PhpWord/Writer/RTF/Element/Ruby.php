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
 * Ruby element RTF writer. Writes {baseText} ({rubyText}) in current paragraph style
 * because RTF does not natively support ruby text.
 */
class Ruby extends AbstractElement
{
    /**
     * Write element.
     *
     * @return string
     */
    public function write()
    {
        /** @var \PhpOffice\PhpWord\Element\Ruby $element */
        $element = $this->element;
        $elementClass = str_replace('\\Writer\\RTF', '', static::class);
        if (!$element instanceof $elementClass || !is_string($element->getBaseTextRun()->getText())) {
            return '';
        }

        $this->getStyles();

        $content = '';
        $content .= $this->writeOpening();
        $content .= '{';
        $content .= $this->writeFontStyle();
        $content .= $this->writeText($element->getBaseTextRun()->getText());
        $rubyText = $element->getRubyTextRun()->getText();
        if ($rubyText !== '') {
            $content .= ' (';
            $content .= $this->writeText($rubyText);
            $content .= ')';
        }
        $content .= '}';
        $content .= $this->writeClosing();

        return $content;
    }
}
