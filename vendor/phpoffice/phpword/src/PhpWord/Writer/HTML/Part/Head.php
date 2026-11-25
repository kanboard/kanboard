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

namespace PhpOffice\PhpWord\Writer\HTML\Part;

use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Style;
use PhpOffice\PhpWord\Style\Font;
use PhpOffice\PhpWord\Style\Paragraph;
use PhpOffice\PhpWord\Style\Table;
use PhpOffice\PhpWord\Writer\HTML\Style\Font as FontStyleWriter;
use PhpOffice\PhpWord\Writer\HTML\Style\Generic as GenericStyleWriter;
use PhpOffice\PhpWord\Writer\HTML\Style\Paragraph as ParagraphStyleWriter;
use PhpOffice\PhpWord\Writer\HTML\Style\Table as TableStyleWriter;

/**
 * HTML head part writer.
 *
 * @since 0.11.0
 */
class Head extends AbstractPart
{
    /**
     * Write part.
     *
     * @return string
     */
    public function write()
    {
        $docProps = $this->getParentWriter()->getPhpWord()->getDocInfo();
        $propertiesMapping = [
            'creator' => 'author',
            'title' => '',
            'description' => '',
            'subject' => '',
            'keywords' => '',
            'category' => '',
            'company' => '',
            'manager' => '',
        ];
        $title = $docProps->getTitle();
        $title = ($title != '') ? $title : 'PHPWord';

        $content = '';

        $content .= '<head>' . PHP_EOL;
        $content .= '<meta charset="UTF-8" />' . PHP_EOL;
        $content .= '<title>' . $title . '</title>' . PHP_EOL;
        foreach ($propertiesMapping as $key => $value) {
            $value = ($value == '') ? $key : $value;
            $method = 'get' . $key;
            if ($docProps->$method() != '') {
                $content .= '<meta name="' . $value . '"'
                    . ' content="'
                    . $this->getParentWriter()->escapeHTML($docProps->$method())
                    . '"'
                    . ' />' . PHP_EOL;
            }
        }
        $content .= $this->writeStyles();
        $content .= '</head>' . PHP_EOL;

        return $content;
    }

    /**
     * Get styles.
     */
    private function writeStyles(): string
    {
        $css = '<style>' . PHP_EOL;
        $defaultFontColor = Settings::getDefaultFontColor();
        // Default styles
        $astarray = [
            'font-family' => $this->getFontFamily(Settings::getDefaultFontName(), $this->getParentWriter()->getDefaultGenericFont()),
            'font-size' => Settings::getDefaultFontSize() . 'pt',
            'color' => "#{$defaultFontColor}",
        ];
        // Mpdf sometimes needs separate tag for body; doesn't harm others.
        $bodyarray = $astarray;

        $defaultWhiteSpace = $this->getParentWriter()->getDefaultWhiteSpace();
        if ($defaultWhiteSpace) {
            $astarray['white-space'] = $defaultWhiteSpace;
        }

        foreach ([
            'body' => $bodyarray,
            '*' => $astarray,
            'a.NoteRef' => [
                'text-decoration' => 'none',
            ],
            'hr' => [
                'height' => '1px',
                'padding' => '0',
                'margin' => '1em 0',
                'border' => '0',
                'border-top' => '1px solid #CCC',
            ],
            'table' => [
                'border' => '1px solid black',
                'border-spacing' => '0px',
                'width ' => '100%',
            ],
            'td' => [
                'border' => '1px solid black',
            ],
        ] as $selector => $style) {
            $styleWriter = new GenericStyleWriter($style);
            $css .= $selector . ' {' . $styleWriter->write() . '}' . PHP_EOL;
        }

        // Custom styles
        $customStyles = Style::getStyles();
        if (is_array($customStyles)) {
            foreach ($customStyles as $name => $style) {
                $styleParagraph = null;
                if ($style instanceof Font) {
                    $styleWriter = new FontStyleWriter($style);
                    if ($style->getStyleType() == 'title') {
                        $name = str_replace('Heading_', 'h', $name);
                        $styleParagraph = $style->getParagraph();
                        $style = $styleParagraph;
                    } else {
                        $name = '.' . $name;
                    }
                    $css .= "{$name} {" . $styleWriter->write() . '}' . PHP_EOL;
                }
                if ($style instanceof Paragraph) {
                    $styleWriter = new ParagraphStyleWriter($style);
                    $styleWriter->setParentWriter($this->getParentWriter());
                    if (!$styleParagraph) {
                        $name = '.' . $name;
                    }
                    if ($name === '.Normal') {
                        $name = "p, $name";
                    }
                    $css .= "{$name} {" . $styleWriter->write() . '}' . PHP_EOL;
                }
                if ($style instanceof Table) {
                    $styleWriter = new TableStyleWriter($style);
                    $css .= ".{$name} {" . $styleWriter->write() . '}' . PHP_EOL;
                }
            }
        }

        $css .= 'body > div + div {page-break-before: always;}' . PHP_EOL;
        $css .= 'div > *:first-child {page-break-before: auto;}' . PHP_EOL;

        $sectionNum = 0;
        foreach ($this->getParentWriter()->getPhpWord()->getSections() as $section) {
            ++$sectionNum;

            $css .= "@page page$sectionNum {";

            $paperSize = $section->getStyle()->getPaperSize();
            $orientation = $section->getStyle()->getOrientation();
            if ($this->getParentWriter()->isPdf()) {
                if ($orientation === 'landscape') {
                    $paperSize .= '-L';
                }
                $css .= "sheet-size: $paperSize; ";
            } else {
                $css .= "size: $paperSize $orientation; ";
            }

            $css .= 'margin-right: ' . (string) ($section->getStyle()->getMarginRight() / Converter::INCH_TO_TWIP) . 'in; ';
            $css .= 'margin-left: ' . (string) ($section->getStyle()->getMarginLeft() / Converter::INCH_TO_TWIP) . 'in; ';
            $css .= 'margin-top: ' . (string) ($section->getStyle()->getMarginTop() / Converter::INCH_TO_TWIP) . 'in; ';
            $css .= 'margin-bottom: ' . (string) ($section->getStyle()->getMarginBottom() / Converter::INCH_TO_TWIP) . 'in; ';
            $css .= '}' . PHP_EOL;
        }

        $css .= '</style>' . PHP_EOL;

        return $css;
    }

    /**
     * Set font and alternates for css font-family.
     */
    private function getFontFamily(string $font, string $genericFont): string
    {
        if (empty($font)) {
            return '';
        }
        $fontfamily = "'" . htmlspecialchars($font, ENT_QUOTES, 'UTF-8') . "'";
        if (!empty($genericFont)) {
            $fontfamily .= ", $genericFont";
        }

        return $fontfamily;
    }
}
