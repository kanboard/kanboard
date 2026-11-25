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

use PhpOffice\PhpWord\Style\Font;
use PhpOffice\PhpWord\Style\Paragraph;

/**
 * Text break element.
 */
class TextBreak extends AbstractElement
{
    /**
     * Paragraph style.
     *
     * @var null|Paragraph|string
     */
    private $paragraphStyle;

    /**
     * Text style.
     *
     * @var null|Font|string
     */
    private $fontStyle;

    /**
     * Create a new TextBreak Element.
     *
     * @param mixed $fontStyle
     * @param mixed $paragraphStyle
     */
    public function __construct($fontStyle = null, $paragraphStyle = null)
    {
        if (null !== $paragraphStyle) {
            $paragraphStyle = $this->setParagraphStyle($paragraphStyle);
        }
        if (null !== $fontStyle) {
            $this->setFontStyle($fontStyle, $paragraphStyle);
        }
    }

    /**
     * Set Text style.
     *
     * @param mixed $style
     * @param mixed $paragraphStyle
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
        } else {
            $this->fontStyle = $style;
            $this->setParagraphStyle($paragraphStyle);
        }

        return $this->fontStyle;
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
     * Set Paragraph style.
     *
     * @param   array|Paragraph|string $style
     *
     * @return  Paragraph|string
     */
    public function setParagraphStyle($style = null)
    {
        if (is_array($style)) {
            $this->paragraphStyle = new Paragraph();
            $this->paragraphStyle->setStyleByArray($style);
        } elseif ($style instanceof Paragraph) {
            $this->paragraphStyle = $style;
        } else {
            $this->paragraphStyle = $style;
        }

        return $this->paragraphStyle;
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
     * Has font/paragraph style defined.
     *
     * @return bool
     */
    public function hasStyle()
    {
        return null !== $this->fontStyle || null !== $this->paragraphStyle;
    }
}
