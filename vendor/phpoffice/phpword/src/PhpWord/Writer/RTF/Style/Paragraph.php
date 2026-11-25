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

namespace PhpOffice\PhpWord\Writer\RTF\Style;

use PhpOffice\PhpWord\SimpleType\Jc;

/**
 * RTF paragraph style writer.
 *
 * @since 0.11.0
 */
class Paragraph extends AbstractStyle
{
    /**
     * Depth of table container nested level; Primarily used for RTF writer/reader.
     *
     * 0 = Not in a table; 1 = in a table; 2 = in a table inside another table, etc.
     *
     * @var int
     */
    private $nestedLevel = 0;

    private const LEFT = Jc::LEFT;
    private const RIGHT = Jc::RIGHT;
    private const JUSTIFY = Jc::JUSTIFY;

    /**
     * Write style.
     *
     * @return string
     */
    public function write()
    {
        $style = $this->getStyle();
        if (!$style instanceof \PhpOffice\PhpWord\Style\Paragraph) {
            return '';
        }

        $alignments = [
            Jc::START => '\ql',
            Jc::END => '\qr',
            Jc::CENTER => '\qc',
            Jc::BOTH => '\qj',
            self::LEFT => '\ql',
            self::RIGHT => '\qr',
            self::JUSTIFY => '\qj',
        ];
        $bidiAlignments = [
            Jc::START => '\qr',
            Jc::END => '\ql',
            Jc::CENTER => '\qc',
            Jc::BOTH => '\qj',
            self::LEFT => '\ql',
            self::RIGHT => '\qr',
            self::JUSTIFY => '\qj',
        ];

        $spaceAfter = $style->getSpaceAfter();
        $spaceBefore = $style->getSpaceBefore();

        $content = '';
        if ($this->nestedLevel == 0) {
            $content .= '\pard\nowidctlpar ';
        }
        $alignment = $style->getAlignment();
        $bidi = $style->isBidi();
        if ($alignment === '' && $bidi !== null) {
            $alignment = Jc::START;
        }
        if (isset($alignments[$alignment])) {
            $content .= $bidi ? $bidiAlignments[$alignment] : $alignments[$alignment];
        }
        $content .= $this->writeIndentation($style->getIndentation());
        $content .= $this->getValueIf($spaceBefore !== null, '\sb' . round($spaceBefore ?? 0));
        $content .= $this->getValueIf($spaceAfter !== null, '\sa' . round($spaceAfter ?? 0));
        $lineHeight = $style->getLineHeight();
        if ($lineHeight) {
            $lineHeightAdjusted = (int) ($lineHeight * 240);
            $content .= "\\sl$lineHeightAdjusted\\slmult1";
        }
        if ($style->hasPageBreakBefore()) {
            $content .= '\\page';
        }

        $styles = $style->getStyleValues();
        $content .= $this->writeTabs($styles['tabs']);

        return $content;
    }

    /**
     * Writes an \PhpOffice\PhpWord\Style\Indentation.
     *
     * @param null|\PhpOffice\PhpWord\Style\Indentation $indent
     *
     * @return string
     */
    private function writeIndentation($indent = null)
    {
        if (isset($indent) && $indent instanceof \PhpOffice\PhpWord\Style\Indentation) {
            $writer = new Indentation($indent);

            return $writer->write();
        }

        return '';
    }

    /**
     * Writes tabs.
     *
     * @param \PhpOffice\PhpWord\Style\Tab[] $tabs
     *
     * @return string
     */
    private function writeTabs($tabs = null)
    {
        $content = '';
        if (!empty($tabs)) {
            foreach ($tabs as $tab) {
                $styleWriter = new Tab($tab);
                $content .= $styleWriter->write();
            }
        }

        return $content;
    }

    /**
     * Set nested level.
     *
     * @param int $value
     */
    public function setNestedLevel($value): void
    {
        $this->nestedLevel = $value;
    }
}
