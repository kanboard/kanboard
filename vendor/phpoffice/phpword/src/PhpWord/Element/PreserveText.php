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
 * Preserve text/field element.
 */
class PreserveText extends AbstractElement
{
    /**
     * Text content.
     *
     * @var null|array|string
     */
    private $text;

    /**
     * Text style.
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
     * Create a new Preserve Text Element.
     *
     * @param string $text
     * @param mixed $fontStyle
     * @param mixed $paragraphStyle
     */
    public function __construct($text = null, $fontStyle = null, $paragraphStyle = null)
    {
        $this->fontStyle = $this->setNewStyle(new Font('text'), $fontStyle);
        $this->paragraphStyle = $this->setNewStyle(new Paragraph(), $paragraphStyle);

        $this->text = SharedText::toUTF8($text);
        $matches = preg_split('/({.*?})/', $this->text ?? '', -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        if (isset($matches[0])) {
            $this->text = $matches;
        }
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
     * Get Text content.
     *
     * @return null|array|string
     */
    public function getText()
    {
        return $this->text;
    }
}
