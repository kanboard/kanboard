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

namespace PhpOffice\PhpWord\Writer\Word2007\Style;

use PhpOffice\PhpWord\Shared\XMLWriter;
use PhpOffice\PhpWord\Style\Frame as FrameStyle;
use PhpOffice\PhpWord\Writer\Word2007\Element\ParagraphAlignment;

/**
 * Frame style writer.
 *
 * @since 0.12.0
 */
class Frame extends AbstractStyle
{
    const PHP_32BIT_INT_MAX = 2147483647;

    /**
     * Write style.
     */
    public function write(): void
    {
        $style = $this->getStyle();
        if (!$style instanceof FrameStyle) {
            return;
        }
        $xmlWriter = $this->getXmlWriter();

        $maxZIndex = min(PHP_INT_MAX, self::PHP_32BIT_INT_MAX);
        $zIndices = [FrameStyle::WRAP_INFRONT => $maxZIndex, FrameStyle::WRAP_BEHIND => -$maxZIndex];

        $properties = [
            'width' => 'width',
            'height' => 'height',
            'left' => 'margin-left',
            'top' => 'margin-top',
            'wrapDistanceTop' => 'mso-wrap-distance-top',
            'wrapDistanceBottom' => 'mso-wrap-distance-bottom',
            'wrapDistanceLeft' => 'mso-wrap-distance-left',
            'wrapDistanceRight' => 'mso-wrap-distance-right',
        ];
        $sizeStyles = $this->getStyles($style, $properties, $style->getUnit());

        $properties = [
            'pos' => 'position',
            'hPos' => 'mso-position-horizontal',
            'vPos' => 'mso-position-vertical',
            'hPosRelTo' => 'mso-position-horizontal-relative',
            'vPosRelTo' => 'mso-position-vertical-relative',
        ];
        $posStyles = $this->getStyles($style, $properties);

        $styles = array_merge($sizeStyles, $posStyles);

        // zIndex for infront & behind wrap
        $wrap = $style->getWrap();
        if ($wrap !== null && isset($zIndices[$wrap])) {
            $styles['z-index'] = $zIndices[$wrap];
            $wrap = null;
        }

        // Style attribute
        $xmlWriter->writeAttribute('style', $this->assembleStyle($styles));

        $this->writeWrap($xmlWriter, $style, $wrap);
    }

    /**
     * Write alignment.
     */
    public function writeAlignment(): void
    {
        $style = $this->getStyle();
        if (!$style instanceof FrameStyle) {
            return;
        }

        $xmlWriter = $this->getXmlWriter();
        $xmlWriter->startElement('w:pPr');

        if ('' !== $style->getAlignment()) {
            $paragraphAlignment = new ParagraphAlignment($style->getAlignment());
            $xmlWriter->startElement($paragraphAlignment->getName());
            foreach ($paragraphAlignment->getAttributes() as $attributeName => $attributeValue) {
                $xmlWriter->writeAttribute($attributeName, $attributeValue);
            }
            $xmlWriter->endElement();
        }

        $xmlWriter->endElement();
    }

    /**
     * Write wrap.
     *
     * @param string $wrap
     */
    private function writeWrap(XMLWriter $xmlWriter, FrameStyle $style, $wrap): void
    {
        if ($wrap !== null) {
            $xmlWriter->startElement('w10:wrap');
            $xmlWriter->writeAttribute('type', $wrap);

            $relativePositions = [
                FrameStyle::POS_RELTO_MARGIN => 'margin',
                FrameStyle::POS_RELTO_PAGE => 'page',
                FrameStyle::POS_RELTO_TMARGIN => 'margin',
                FrameStyle::POS_RELTO_BMARGIN => 'page',
                FrameStyle::POS_RELTO_LMARGIN => 'margin',
                FrameStyle::POS_RELTO_RMARGIN => 'page',
            ];
            $pos = $style->getPos();
            $hPos = $style->getHPosRelTo();
            $vPos = $style->getVPosRelTo();

            if ($pos == FrameStyle::POS_ABSOLUTE) {
                $xmlWriter->writeAttribute('anchorx', 'page');
                $xmlWriter->writeAttribute('anchory', 'page');
            } elseif ($pos == FrameStyle::POS_RELATIVE) {
                if (isset($relativePositions[$hPos])) {
                    $xmlWriter->writeAttribute('anchorx', $relativePositions[$hPos]);
                }
                if (isset($relativePositions[$vPos])) {
                    $xmlWriter->writeAttribute('anchory', $relativePositions[$vPos]);
                }
            }

            $xmlWriter->endElement(); // w10:wrap
        }
    }

    /**
     * Get style values in associative array.
     *
     * @param array $properties
     * @param string $suffix
     *
     * @return array
     */
    private function getStyles(FrameStyle $style, $properties, $suffix = '')
    {
        $styles = [];

        foreach ($properties as $key => $property) {
            $method = "get{$key}";
            $value = $style->$method();
            if ($value !== null) {
                $styles[$property] = $style->$method() . $suffix;
            }
        }

        return $styles;
    }
}
