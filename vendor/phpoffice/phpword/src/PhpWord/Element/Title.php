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

use InvalidArgumentException;
use PhpOffice\PhpWord\Shared\Text as SharedText;
use PhpOffice\PhpWord\Style;

/**
 * Title element.
 */
class Title extends AbstractElement
{
    /**
     * Title Text content.
     *
     * @var string|TextRun
     */
    private $text;

    /**
     * Title depth.
     *
     * @var int
     */
    private $depth = 1;

    /**
     * Name of the heading style, e.g. 'Heading1'.
     *
     * @var ?string
     */
    private $style;

    /**
     * Is part of collection.
     *
     * @var bool
     */
    protected $collectionRelation = true;

    /**
     * Page number.
     *
     * @var int
     */
    private $pageNumber;

    /**
     * Create a new Title Element.
     *
     * @param string|TextRun $text
     * @param int $depth
     */
    public function __construct($text, $depth = 1, ?int $pageNumber = null)
    {
        if (is_string($text)) {
            $this->text = SharedText::toUTF8($text);
        } elseif ($text instanceof TextRun) {
            $this->text = $text;
        } else {
            throw new InvalidArgumentException('Invalid text, should be a string or a TextRun');
        }

        $this->depth = $depth;
        $styleName = $depth === 0 ? 'Title' : "Heading_{$this->depth}";
        if (array_key_exists($styleName, Style::getStyles())) {
            $this->style = str_replace('_', '', $styleName);
        }

        if ($pageNumber !== null) {
            $this->pageNumber = $pageNumber;
        }
    }

    /**
     * Get Title Text content.
     *
     * @return string|TextRun
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Get depth.
     *
     * @return int
     */
    public function getDepth()
    {
        return $this->depth;
    }

    /**
     * Get Title style.
     *
     * @return ?string
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * Get page number.
     */
    public function getPageNumber(): ?int
    {
        return $this->pageNumber;
    }
}
