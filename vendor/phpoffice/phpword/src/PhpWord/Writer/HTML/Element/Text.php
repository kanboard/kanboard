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

use PhpOffice\PhpWord\Element\TrackChange;
use PhpOffice\PhpWord\Style;
use PhpOffice\PhpWord\Style\Font;
use PhpOffice\PhpWord\Style\Paragraph;
use PhpOffice\PhpWord\Writer\HTML;
use PhpOffice\PhpWord\Writer\HTML\Style\Font as FontStyleWriter;
use PhpOffice\PhpWord\Writer\HTML\Style\Paragraph as ParagraphStyleWriter;

/**
 * Text element HTML writer.
 *
 * @since 0.10.0
 */
class Text extends AbstractElement
{
    /**
     * Text written after opening.
     *
     * @var string
     */
    private $openingText = '';

    /**
     * Text written before closing.
     *
     * @var string
     */
    private $closingText = '';

    /**
     * Opening tags.
     *
     * @var string
     */
    private $openingTags = '';

    /**
     * Closing tag.
     *
     * @var string
     */
    private $closingTags = '';

    /**
     * Write text.
     *
     * @return string
     */
    public function write()
    {
        $this->processFontStyle();

        /** @var \PhpOffice\PhpWord\Element\Text $element Type hint */
        $element = $this->element;

        $text = $this->parentWriter->escapeHTML($element->getText() ?? '');
        if (!$this->withoutP && !trim($text)) {
            $text = '&nbsp;';
        }

        $content = '';
        $content .= $this->writeOpening();
        $content .= $this->openingText;
        $content .= $this->openingTags;
        $content .= $text;
        $content .= $this->closingTags;
        $content .= $this->closingText;
        $content .= $this->writeClosing();

        return $content;
    }

    /**
     * Set opening text.
     *
     * @param string $value
     */
    public function setOpeningText($value): void
    {
        $this->openingText = $value;
    }

    /**
     * Set closing text.
     *
     * @param string $value
     */
    public function setClosingText($value): void
    {
        $this->closingText = $value;
    }

    /**
     * Write opening.
     *
     * @return string
     */
    protected function writeOpening()
    {
        $content = '';
        if (!$this->withoutP) {
            $style = $this->getParagraphStyle();
            $content .= "<p{$style}>";
        }

        //open track change tag
        $content .= $this->writeTrackChangeOpening();

        return $content;
    }

    /**
     * Write ending.
     *
     * @return string
     */
    protected function writeClosing()
    {
        $content = '';

        //close track change tag
        $content .= $this->writeTrackChangeClosing();

        if (!$this->withoutP) {
            $content .= $this->parentWriter->escapeHTML($this->closingText);
            $content .= '</p>' . PHP_EOL;
        }

        return $content;
    }

    /**
     * writes the track change opening tag.
     *
     * @return string the HTML, an empty string if no track change information
     */
    private function writeTrackChangeOpening()
    {
        $changed = $this->element->getTrackChange();
        if ($changed == null) {
            return '';
        }

        $content = '';
        if (($changed->getChangeType() == TrackChange::INSERTED)) {
            $content .= '<ins data-phpword-prop=\'';
        } elseif ($changed->getChangeType() == TrackChange::DELETED) {
            $content .= '<del data-phpword-prop=\'';
        }

        $changedProp = ['changed' => ['author' => $changed->getAuthor(), 'id' => $this->element->getElementId()]];
        if ($changed->getDate() != null) {
            $changedProp['changed']['date'] = $changed->getDate()->format('Y-m-d\TH:i:s\Z');
        }
        $content .= json_encode($changedProp);
        $content .= '\' ';
        $content .= 'title="' . $changed->getAuthor();
        if ($changed->getDate() != null) {
            $dateUser = $changed->getDate()->format('Y-m-d H:i:s');
            $content .= ' - ' . $dateUser;
        }
        $content .= '">';

        return $content;
    }

    /**
     * writes the track change closing tag.
     *
     * @return string the HTML, an empty string if no track change information
     */
    private function writeTrackChangeClosing()
    {
        $changed = $this->element->getTrackChange();
        if ($changed == null) {
            return '';
        }

        $content = '';
        if (($changed->getChangeType() == TrackChange::INSERTED)) {
            $content .= '</ins>';
        } elseif ($changed->getChangeType() == TrackChange::DELETED) {
            $content .= '</del>';
        }

        return $content;
    }

    /**
     * Write paragraph style.
     *
     * @return string
     */
    private function getParagraphStyle()
    {
        /** @var \PhpOffice\PhpWord\Element\Text $element Type hint */
        $element = $this->element;
        $style = '';
        if (!method_exists($element, 'getParagraphStyle')) {
            return $style;
        }

        $paragraphStyle = $element->getParagraphStyle();
        $pStyleIsObject = ($paragraphStyle instanceof Paragraph);
        if ($pStyleIsObject) {
            $styleWriter = new ParagraphStyleWriter($paragraphStyle);
            $styleWriter->setParentWriter($this->parentWriter);
            $style = $styleWriter->write();
        } elseif (is_string($paragraphStyle)) {
            $style = $paragraphStyle;
        }
        if ($style) {
            $attribute = $pStyleIsObject ? 'style' : 'class';
            $style = " {$attribute}=\"{$style}\"";
        }

        return $style;
    }

    /**
     * Get font style.
     */
    private function processFontStyle(): void
    {
        /** @var \PhpOffice\PhpWord\Element\Text $element Type hint */
        $element = $this->element;

        $attributeStyle = $attributeLang = '';
        $lang = null;

        $fontStyle = $element->getFontStyle();
        if ($fontStyle instanceof Font) {
            // Attribute style
            $styleWriter = new FontStyleWriter($fontStyle);
            $fontCSS = $styleWriter->write();
            if ($fontCSS) {
                $attributeStyle = ' style="' . $fontCSS . '"';
            }
            // Attribute Lang
            $lang = $fontStyle->getLang();
        } elseif (!empty($fontStyle)) {
            // Attribute class
            $attributeStyle = ' class="' . $fontStyle . '"';
            // Attribute Lang
            /** @var Font $cssClassStyle */
            $cssClassStyle = Style::getStyle($fontStyle);
            if ($cssClassStyle !== null && method_exists($cssClassStyle, 'getLang')) {
                $lang = $cssClassStyle->getLang();
            }
        }

        if ($lang) {
            $attributeLang = $lang->getLatin();
            if (!$attributeLang) {
                $attributeLang = $lang->getEastAsia();
            }
            if (!$attributeLang) {
                $attributeLang = $lang->getBidirectional();
            }
            if ($attributeLang) {
                $attributeLang = " lang='$attributeLang'";
            }
        }

        if ($attributeStyle || $attributeLang) {
            $this->openingTags = "<span$attributeLang$attributeStyle>";
            $this->closingTags = '</span>';
        }
    }
}
