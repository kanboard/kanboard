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

namespace PhpOffice\PhpWord\Writer\Word2007\Part;

/**
 * Word2007 endnotes part writer: word/endnotes.xml.
 */
class Endnotes extends Footnotes
{
    /**
     * Name of XML root element.
     *
     * @var string
     */
    protected $rootNode = 'w:endnotes';

    /**
     * Name of XML node element.
     *
     * @var string
     */
    protected $elementNode = 'w:endnote';

    /**
     * Name of XML reference element.
     *
     * @var string
     */
    protected $refNode = 'w:endnoteRef';

    /**
     * Reference style name.
     *
     * @var string
     */
    protected $refStyle = 'EndnoteReference';
}
