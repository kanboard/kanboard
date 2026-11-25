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
 * Title element RTF writer; extends from text.
 *
 * @since 0.11.0
 */
class Title extends Text
{
    protected function getStyles(): void
    {
        /** @var \PhpOffice\PhpWord\Element\Title $element Type hint */
        $element = $this->element;
        $style = $element->getStyle();
        $style = str_replace('Heading', 'Heading_', $style ?? '');
        $style = \PhpOffice\PhpWord\Style::getStyle($style);
        if ($style instanceof \PhpOffice\PhpWord\Style\Font) {
            $this->fontStyle = $style;
            $pstyle = $style->getParagraph();
            if ($pstyle instanceof \PhpOffice\PhpWord\Style\Paragraph && $pstyle->hasPageBreakBefore()) {
                $sect = $element->getParent();
                if ($sect instanceof \PhpOffice\PhpWord\Element\Section) {
                    $elems = $sect->getElements();
                    if ($elems[0] === $element) {
                        $pstyle = clone $pstyle;
                        $pstyle->setPageBreakBefore(false);
                    }
                }
            }
            $this->paragraphStyle = $pstyle;
        }
    }

    /**
     * Write element.
     *
     * @return string
     */
    public function write()
    {
        /** @var \PhpOffice\PhpWord\Element\Title $element Type hint */
        $element = $this->element;
        $elementClass = str_replace('\\Writer\\RTF', '', static::class);
        if (!$element instanceof $elementClass) {
            return '';
        }

        $textToWrite = $element->getText();
        if ($textToWrite instanceof \PhpOffice\PhpWord\Element\TextRun) {
            $textToWrite = $textToWrite->getText(); // gets text from TextRun
        }

        $this->getStyles();

        $content = '';

        $content .= $this->writeOpening();
        $endout = '';
        $style = $element->getStyle();
        if (is_string($style)) {
            $style = str_replace('Heading', '', $style);
            if ("$style" !== '') {
                $style = (int) $style - 1;
                if ($style >= 0 && $style <= 8) {
                    $content .= '{\\outlinelevel' . $style;
                    $endout = '}';
                }
            }
        }

        $content .= '{';
        $content .= $this->writeFontStyle();
        $content .= $this->writeText($textToWrite);
        $content .= '}';
        $content .= $this->writeClosing();
        $content .= $endout;

        return $content;
    }
}
