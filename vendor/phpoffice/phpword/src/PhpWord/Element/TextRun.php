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

use PhpOffice\PhpWord\Style\Paragraph;

/**
 * Textrun/paragraph element.
 */
class TextRun extends AbstractContainer
{
    /**
     * @var string Container type
     */
    protected $container = 'TextRun';

    /**
     * Paragraph style.
     *
     * @var Paragraph|string
     */
    protected $paragraphStyle;

    /**
     * Create new instance.
     *
     * @param array|Paragraph|string $paragraphStyle
     */
    public function __construct($paragraphStyle = null)
    {
        $this->paragraphStyle = $this->setParagraphStyle($paragraphStyle);
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

    public function getText(): string
    {
        $outstr = '';
        foreach ($this->getElements() as $element) {
            if ($element instanceof Text) {
                $outstr .= $element->getText();
            } elseif ($element instanceof Ruby) {
                $outstr .= $element->getBaseTextRun()->getText() .
                    ' (' . $element->getRubyTextRun()->getText() . ')';
            }
        }

        return $outstr;
    }
}
