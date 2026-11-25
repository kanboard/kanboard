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

namespace PhpOffice\PhpWord\Writer\ODText\Style;

use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\Style;

/**
 * Font style writer.
 *
 * @since 0.10.0
 */
class Paragraph extends AbstractStyle
{
    private const BIDI_MAP = [
        Jc::END => Jc::LEFT,
        Jc::START => Jc::RIGHT,
    ];

    private const NON_BIDI_MAP = [
        Jc::START => Jc::LEFT,
        Jc::END => Jc::RIGHT,
    ];

    /**
     * Write style.
     */
    public function write(): void
    {
        $style = $this->getStyle();
        if (!$style instanceof Style\Paragraph) {
            return;
        }
        $xmlWriter = $this->getXmlWriter();

        $marginTop = $style->getSpaceBefore();
        $marginBottom = $style->getSpaceAfter();

        $xmlWriter->startElement('style:style');

        $styleName = (string) $style->getStyleName();
        $styleAuto = false;
        $mpm = '';
        $psm = '';
        $pagestart = -1;
        $breakafter = $breakbefore = $breakauto = false;
        if ($style->isAuto()) {
            if (substr($styleName, 0, 2) === 'PB') {
                $styleAuto = true;
                $breakafter = true;
            } elseif (substr($styleName, 0, 2) === 'SB') {
                $styleAuto = true;
                $mpm = 'Standard' . substr($styleName, 2);
                $psn = $style->getNumLevel();
                $pagestart = $psn;
            } elseif (substr($styleName, 0, 2) === 'HD') {
                $styleAuto = true;
                $psm = 'Heading_' . substr($styleName, 2);
                $stylep = Style::getStyle($psm);
                if ($stylep instanceof Style\Font) {
                    if (method_exists($stylep, 'getParagraph')) {
                        $stylep = $stylep->getParagraph();
                    }
                }
                if ($stylep instanceof Style\Paragraph) {
                    if ($stylep->hasPageBreakBefore()) {
                        $breakbefore = true;
                    }
                }
            } elseif (substr($styleName, 0, 2) === 'HE') {
                $styleAuto = true;
                $psm = 'Heading_' . substr($styleName, 2);
                $breakauto = true;
            } else {
                $styleAuto = true;
                $psm = 'Normal';
                if (preg_match('/^P\\d+_(\\w+)$/', $styleName, $matches)) {
                    $psm = $matches[1];
                }
            }
        }

        $xmlWriter->writeAttribute('style:name', $style->getStyleName());
        $xmlWriter->writeAttribute('style:family', 'paragraph');
        if ($styleAuto) {
            $xmlWriter->writeAttributeIf($psm !== '', 'style:parent-style-name', $psm);
            $xmlWriter->writeAttributeIf($mpm !== '', 'style:master-page-name', $mpm);
        }

        $xmlWriter->startElement('style:paragraph-properties');
        if ($styleAuto) {
            if ($breakafter) {
                $xmlWriter->writeAttribute('fo:break-after', 'page');
                $xmlWriter->writeAttribute('fo:margin-top', '0cm');
                $xmlWriter->writeAttribute('fo:margin-bottom', '0cm');
            } elseif ($breakbefore) {
                $xmlWriter->writeAttribute('fo:break-before', 'page');
            } elseif ($breakauto) {
                $xmlWriter->writeAttribute('fo:break-before', 'auto');
            }
            if ($pagestart > 0) {
                $xmlWriter->writeAttribute('style:page-number', $pagestart);
            }
        }
        if (!$breakafter && !$breakbefore && !$breakauto) {
            $twipToPoint = Converter::INCH_TO_TWIP / Converter::INCH_TO_POINT; // 20
            $xmlWriter->writeAttributeIf($marginTop !== null, 'fo:margin-top', ($marginTop / $twipToPoint) . 'pt');
            $xmlWriter->writeAttributeIf($marginBottom !== null, 'fo:margin-bottom', ($marginBottom / $twipToPoint) . 'pt');
        }
        $alignment = $style->getAlignment();
        $bidi = $style->isBidi();
        $defaultRtl = Settings::isDefaultRtl();
        if ($alignment === '' && $bidi !== null) {
            $alignment = Jc::START;
        }
        if ($bidi) {
            $alignment = self::BIDI_MAP[$alignment] ?? $alignment;
        } elseif ($defaultRtl !== null) {
            $alignment = self::NON_BIDI_MAP[$alignment] ?? $alignment;
        }
        $xmlWriter->writeAttributeIf($alignment !== '', 'fo:text-align', $alignment);
        $temp = $style->getLineHeight();
        $xmlWriter->writeAttributeIf($temp !== null, 'fo:line-height', ((string) ($temp * 100) . '%'));
        $xmlWriter->writeAttributeIf($style->hasPageBreakBefore() === true, 'fo:break-before', 'page');

        $tabs = $style->getTabs();
        if ($tabs !== null && count($tabs) > 0) {
            $xmlWriter->startElement('style:tab-stops');
            foreach ($tabs as $tab) {
                $xmlWriter->startElement('style:tab-stop');
                $xmlWriter->writeAttribute('style:type', $tab->getType());
                $xmlWriter->writeAttribute('style:position', (string) ($tab->getPosition() / Converter::INCH_TO_TWIP) . 'in');
                $xmlWriter->endElement();
            }
            $xmlWriter->endElement();
        }

        //Right to left
        $xmlWriter->writeAttributeIf($style->isBidi(), 'style:writing-mode', 'rl-tb');

        //Indentation
        $indent = $style->getIndentation();
        //if ($indent instanceof \PhpOffice\PhpWord\Style\Indentation) {
        if (!empty($indent)) {
            $marg = $indent->getLeft();
            $xmlWriter->writeAttributeIf($marg !== null, 'fo:margin-left', (string) ($marg / Converter::INCH_TO_TWIP) . 'in');
            $marg = $indent->getRight();
            $xmlWriter->writeAttributeIf($marg !== null, 'fo:margin-right', (string) ($marg / Converter::INCH_TO_TWIP) . 'in');
        }

        $xmlWriter->endElement(); //style:paragraph-properties

        if ($styleAuto && substr($styleName, 0, 2) === 'SB') {
            $xmlWriter->startElement('style:text-properties');
            $xmlWriter->writeAttribute('text:display', 'none');
            $xmlWriter->endElement();
        }

        $xmlWriter->endElement(); //style:style
    }
}
