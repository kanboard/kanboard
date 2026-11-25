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

use PhpOffice\PhpWord\Shared\Text as SharedText;
use PhpOffice\PhpWord\Style\Font;
use PhpOffice\PhpWord\Style\Paragraph;

/**
 * Link element.
 */
class Link extends AbstractElement
{
    /**
     * Link source.
     *
     * @var string
     */
    private $source;

    /**
     * Link text.
     *
     * @var string
     */
    private $text;

    /**
     * Font style.
     *
     * @var null|Font|string
     */
    private $fontStyle;

    /**
     * Paragraph style.
     *
     * @var null|Paragraph|string
     */
    private $paragraphStyle;

    /**
     * Has media relation flag; true for Link, Image, and Object.
     *
     * @var bool
     */
    protected $mediaRelation = true;

    /**
     * Has internal flag - anchor to internal bookmark.
     *
     * @var bool
     */
    protected $internal = false;

    /**
     * Create a new Link Element.
     *
     * @param string $source
     * @param string $text
     * @param mixed $fontStyle
     * @param mixed $paragraphStyle
     * @param bool $internal
     */
    public function __construct($source, $text = null, $fontStyle = null, $paragraphStyle = null, $internal = false)
    {
        $this->source = SharedText::toUTF8($source);
        $this->text = null === $text ? $this->source : SharedText::toUTF8($text);
        $this->fontStyle = $this->setNewStyle(new Font('text'), $fontStyle);
        $this->paragraphStyle = $this->setNewStyle(new Paragraph(), $paragraphStyle);
        $this->internal = $internal;
    }

    /**
     * Get link source.
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Get link text.
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * Get Text style.
     *
     * @return null|Font|string
     */
    public function getFontStyle()
    {
        return $this->fontStyle;
    }

    /**
     * Get Paragraph style.
     *
     * @return null|Paragraph|string
     */
    public function getParagraphStyle()
    {
        return $this->paragraphStyle;
    }

    /**
     * is internal.
     *
     * @return bool
     */
    public function isInternal()
    {
        return $this->internal;
    }
}
