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
 * Text element.
 */
class Text extends AbstractElement
{
    /**
     * Text content.
     *
     * @var ?string
     */
    protected $text;

    /**
     * Text style.
     *
     * @var Font|string
     */
    protected $fontStyle;

    /**
     * Paragraph style.
     *
     * @var Paragraph|string
     */
    protected $paragraphStyle;

    /**
     * Create a new Text Element.
     *
     * @param string $text
     * @param mixed $fontStyle
     * @param mixed $paragraphStyle
     */
    public function __construct($text = null, $fontStyle = null, $paragraphStyle = null)
    {
        $this->setText($text);
        $paragraphStyle = $this->setParagraphStyle($paragraphStyle);
        $this->setFontStyle($fontStyle, $paragraphStyle);
    }

    /**
     * Set Text style.
     *
     * @param array|Font|string $style
     * @param array|Paragraph|string $paragraphStyle
     *
     * @return Font|string
     */
    public function setFontStyle($style = null, $paragraphStyle = null)
    {
        if ($style instanceof Font) {
            $this->fontStyle = $style;
            $this->setParagraphStyle($paragraphStyle);
        } elseif (is_array($style)) {
            $this->fontStyle = new Font('text', $paragraphStyle);
            $this->fontStyle->setStyleByArray($style);
        } elseif (null === $style) {
            $this->fontStyle = new Font('text', $paragraphStyle);
        } else {
            $this->fontStyle = $style;
            $this->setParagraphStyle($paragraphStyle);
        }

        return $this->fontStyle;
    }

    /**
     * Get Text style.
     *
     * @return Font|string
     */
    public function getFontStyle()
    {
        return $this->fontStyle;
    }

    /**
     * Set Paragraph style.
     *
     * @param array|Paragraph|string $style
     *
     * @return Paragraph|string
     */
    public function setParagraphStyle($style = null)
    {
        if (is_array($style)) {
            $this->paragraphStyle = new Paragraph();
            $this->paragraphStyle->setStyleByArray($style);
        } elseif ($style instanceof Paragraph) {
            $this->paragraphStyle = $style;
        } elseif (null === $style) {
            $this->paragraphStyle = new Paragraph();
        } else {
            $this->paragraphStyle = $style;
        }

        return $this->paragraphStyle;
    }

    /**
     * Get Paragraph style.
     *
     * @return Paragraph|string
     */
    public function getParagraphStyle()
    {
        return $this->paragraphStyle;
    }

    /**
     * Set text content.
     *
     * @param string $text
     *
     * @return self
     */
    public function setText($text)
    {
        $this->text = SharedText::toUTF8($text);

        return $this;
    }

    /**
     * Get Text content.
     */
    public function getText(): ?string
    {
        return $this->text;
    }
}
