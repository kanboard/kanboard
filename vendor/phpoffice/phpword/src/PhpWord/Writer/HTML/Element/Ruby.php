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

namespace PhpOffice\PhpWord\Writer\HTML\Element;

use PhpOffice\PhpWord\ComplexType\RubyProperties;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Style\Paragraph;
use PhpOffice\PhpWord\Writer\HTML\Style\Paragraph as ParagraphStyleWriter;

/**
 * Ruby element HTML writer.
 */
class Ruby extends AbstractElement
{
    /**
     * Write text.
     *
     * @return string
     */
    public function write()
    {
        // $this->processFontStyle();

        /** @var \PhpOffice\PhpWord\Element\Ruby $element Type hint */
        $element = $this->element;

        $baseText = $this->parentWriter->escapeHTML($element->getBaseTextRun()->getText());
        $rubyText = $this->parentWriter->escapeHTML($element->getRubyTextRun()->getText());

        $rubyTagPropertyCSS = $this->getPropertyCssForRubyTag($element->getProperties());
        $lang = $element->getProperties()->getLanguageId();
        $content = "<ruby {$this->getParagraphStyleForTextRun($element->getBaseTextRun(), $rubyTagPropertyCSS)} lang=\"{$lang}\">";
        $content .= $baseText;
        $content .= ' <rp>(</rp>';
        $rtTagPropertyCSS = $this->getPropertyCssForRtTag($element->getProperties());
        $content .= "<rt {$this->getParagraphStyleForTextRun($element->getRubyTextRun(), $rtTagPropertyCSS)}>";
        $content .= $rubyText;
        $content .= '</rt>';
        $content .= '<rp>)</rp>';
        $content .= '</ruby>';

        return $content;
    }

    /**
     * Get property CSS for the <ruby> tag.
     */
    private function getPropertyCssForRubyTag(RubyProperties $properties): string
    {
        // alignment CSS: https://developer.mozilla.org/en-US/docs/Web/CSS/ruby-align
        $alignment = 'space-between';
        switch ($properties->getAlignment()) {
            case RubyProperties::ALIGNMENT_CENTER:
                $alignment = 'center';

                break;
            case RubyProperties::ALIGNMENT_LEFT:
                $alignment = 'start';

                break;
            default:
                $alignment = 'space-between';

                break;
        }

        return
            'font-size:' . $properties->getFontSizeForBaseText() . 'pt' . ';' .
            'ruby-align:' . $alignment . ';';
    }

    /**
     * Get property CSS for the <rt> tag.
     */
    private function getPropertyCssForRtTag(RubyProperties $properties): string
    {
        // alignment CSS: https://developer.mozilla.org/en-US/docs/Web/CSS/ruby-align
        return 'font-size:' . $properties->getFontFaceSize() . 'pt' . ';';
    }

    /**
     * Write paragraph style for a given TextRun.
     */
    private function getParagraphStyleForTextRun(TextRun $textRun, string $extraCSS): string
    {
        $style = '';
        if (!method_exists($textRun, 'getParagraphStyle')) {
            return $style;
        }

        $paragraphStyle = $textRun->getParagraphStyle();
        $pStyleIsObject = ($paragraphStyle instanceof Paragraph);
        if ($pStyleIsObject) {
            $styleWriter = new ParagraphStyleWriter($paragraphStyle);
            $styleWriter->setParentWriter($this->parentWriter);
            $style = $styleWriter->write();
        } elseif (is_string($paragraphStyle)) {
            $style = $paragraphStyle;
        }
        if ($style !== null && $style !== '') {
            if ($pStyleIsObject) {
                // CSS pairs (style="...")
                $style = " style=\"{$style}{$extraCSS}\"";
            } else {
                // class name; need to append extra styles manually
                $style = " class=\"{$style}\" style=\"{$extraCSS}\"";
            }
        } elseif ($extraCSS !== '') {
            $style = " style=\"{$extraCSS}\"";
        }

        return $style;
    }
}
