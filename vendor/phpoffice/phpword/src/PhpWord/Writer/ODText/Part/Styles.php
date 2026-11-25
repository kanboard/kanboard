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

namespace PhpOffice\PhpWord\Writer\ODText\Part;

use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Shared\XMLWriter;
use PhpOffice\PhpWord\Style;

/**
 * ODText styles part writer: styles.xml.
 */
class Styles extends AbstractPart
{
    /**
     * Write part.
     *
     * @return string
     */
    public function write()
    {
        $xmlWriter = $this->getXmlWriter();

        // XML header
        $xmlWriter->startDocument('1.0', 'UTF-8');
        $xmlWriter->startElement('office:document-styles');
        $this->writeCommonRootAttributes($xmlWriter);

        // Font declarations
        $this->writeFontFaces($xmlWriter);

        // Office styles
        $xmlWriter->startElement('office:styles');
        $this->writeDefault($xmlWriter);
        $this->writeNamed($xmlWriter);
        $xmlWriter->endElement();

        // Automatic styles
        $xmlWriter->startElement('office:automatic-styles');
        $this->writePageLayout($xmlWriter);
        $xmlWriter->endElement(); // office:automatic-styles

        // Master style
        $this->writeMaster($xmlWriter);

        $xmlWriter->endElement(); // office:document-styles

        return $xmlWriter->getData();
    }

    /**
     * Write default styles.
     */
    private function writeDefault(XMLWriter $xmlWriter): void
    {
        $xmlWriter->startElement('style:default-style');
        $xmlWriter->writeAttribute('style:family', 'paragraph');

        // Paragraph
        $xmlWriter->startElement('style:paragraph-properties');
        $xmlWriter->writeAttribute('fo:hyphenation-ladder-count', 'no-limit');
        $xmlWriter->writeAttribute('style:text-autospace', 'ideograph-alpha');
        $xmlWriter->writeAttribute('style:punctuation-wrap', 'hanging');
        $xmlWriter->writeAttribute('style:line-break', 'strict');
        $xmlWriter->writeAttribute('style:tab-stop-distance', '1.249cm');
        $xmlWriter->writeAttribute('style:writing-mode', 'page');
        $xmlWriter->endElement(); // style:paragraph-properties

        $language = $this->getParentWriter()->getPhpWord()->getSettings()->getThemeFontLang();
        $latinLang = $language != null && is_string($language->getLatin()) ? explode('-', $language->getLatin()) : ['fr', 'FR'];
        $asianLang = $language != null && is_string($language->getEastAsia()) ? explode('-', $language->getEastAsia()) : ['zh', 'CN'];
        $complexLang = $language != null && is_string($language->getBidirectional()) ? explode('-', $language->getBidirectional()) : ['hi', 'IN'];
        if ($this->getParentWriter()->getPhpWord()->getSettings()->hasHideGrammaticalErrors()) {
            $latinLang = $asianLang = $complexLang = ['zxx', 'none'];
        }

        // Font
        $xmlWriter->startElement('style:text-properties');
        $xmlWriter->writeAttribute('style:use-window-font-color', 'false');
        $xmlWriter->writeAttribute('style:font-name', Settings::getDefaultFontName());
        $xmlWriter->writeAttribute('fo:font-size', Settings::getDefaultFontSize() . 'pt');
        $xmlWriter->writeAttribute('fo:language', $latinLang[0]);
        $xmlWriter->writeAttribute('fo:country', $latinLang[1]);
        $xmlWriter->writeAttribute('fo:color', '#' . Settings::getDefaultFontColor());
        $xmlWriter->writeAttribute('style:letter-kerning', 'true');
        $xmlWriter->writeAttribute('style:font-name-asian', Settings::getDefaultFontName() . '2');
        $xmlWriter->writeAttribute('style:font-size-asian', Settings::getDefaultFontSize() . 'pt');
        $xmlWriter->writeAttribute('style:language-asian', $asianLang[0]);
        $xmlWriter->writeAttribute('style:country-asian', $asianLang[1]);
        $xmlWriter->writeAttribute('style:font-name-complex', Settings::getDefaultFontName() . '2');
        $xmlWriter->writeAttribute('style:font-size-complex', Settings::getDefaultFontSize() . 'pt');
        $xmlWriter->writeAttribute('style:language-complex', $complexLang[0]);
        $xmlWriter->writeAttribute('style:country-complex', $complexLang[1]);
        $xmlWriter->writeAttribute('fo:hyphenate', 'false');
        $xmlWriter->writeAttribute('fo:hyphenation-remain-char-count', '2');
        $xmlWriter->writeAttribute('fo:hyphenation-push-char-count', '2');
        $xmlWriter->endElement(); // style:text-properties

        $xmlWriter->endElement(); // style:default-style
    }

    /**
     * Write named styles.
     */
    private function writeNamed(XMLWriter $xmlWriter): void
    {
        $styles = Style::getStyles();
        if (count($styles) > 0) {
            foreach ($styles as $style) {
                if ($style->isAuto() === false) {
                    $styleClass = str_replace('\\Style\\', '\\Writer\\ODText\\Style\\', get_class($style));
                    if (class_exists($styleClass)) {
                        /** @var \PhpOffice\PhpWord\Writer\ODText\Style\AbstractStyle $styleWriter Type hint */
                        $styleWriter = new $styleClass($xmlWriter, $style);
                        $styleWriter->write();
                    }
                }
            }
        }
    }

    /**
     * Convert int in twips to inches/cm then to string and append unit.
     *
     * @param float|int $twips
     * @param float $factor
     * return string
     */
    private static function cvttwiptostr($twips, $factor = 1.0)
    {
        $ins = (string) ($twips * $factor / Converter::INCH_TO_TWIP) . 'in';
        $cms = (string) ($twips * $factor * Converter::INCH_TO_CM / Converter::INCH_TO_TWIP) . 'cm';

        return (strlen($ins) < strlen($cms)) ? $ins : $cms;
    }

    /**
     * call writePageLayoutIndiv to write page layout styles for each page.
     */
    private function writePageLayout(XMLWriter $xmlWriter): void
    {
        $sections = $this->getParentWriter()->getPhpWord()->getSections();
        $countsects = count($sections);
        for ($i = 0; $i < $countsects; ++$i) {
            $this->writePageLayoutIndiv($xmlWriter, $sections[$i], $i + 1);
        }
    }

    /**
     * Write page layout styles.
     *
     * @param \PhpOffice\PhpWord\Element\Section $section
     * @param int $sectionNbr
     */
    private function writePageLayoutIndiv(XMLWriter $xmlWriter, $section, $sectionNbr): void
    {
        $sty = $section->getStyle();
        if (count($section->getHeaders()) > 0) {
            $topfactor = 0.5;
        } else {
            $topfactor = 1.0;
        }
        if (count($section->getFooters()) > 0) {
            $botfactor = 0.5;
        } else {
            $botfactor = 1.0;
        }
        $orient = $sty->getOrientation();
        $pwidth = self::cvttwiptostr($sty->getPageSizeW());
        $pheight = self::cvttwiptostr($sty->getPageSizeH());
        $mtop = self::cvttwiptostr($sty->getMarginTop(), $topfactor);
        $mbottom = self::cvttwiptostr($sty->getMarginBottom(), $botfactor);
        $mleft = self::cvttwiptostr($sty->getMarginRight());
        $mright = self::cvttwiptostr($sty->getMarginLeft());

        $xmlWriter->startElement('style:page-layout');
        $xmlWriter->writeAttribute('style:name', "Mpm$sectionNbr");

        $xmlWriter->startElement('style:page-layout-properties');
        $xmlWriter->writeAttribute('fo:page-width', $pwidth);
        $xmlWriter->writeAttribute('fo:page-height', $pheight);
        $xmlWriter->writeAttribute('style:num-format', '1');
        $xmlWriter->writeAttribute('style:print-orientation', $orient);
        $xmlWriter->writeAttribute('fo:margin-top', $mtop);
        $xmlWriter->writeAttribute('fo:margin-bottom', $mbottom);
        $xmlWriter->writeAttribute('fo:margin-left', $mleft);
        $xmlWriter->writeAttribute('fo:margin-right', $mright);
        $xmlWriter->writeAttribute('style:writing-mode', 'lr-tb');
        $xmlWriter->writeAttribute('style:layout-grid-color', '#c0c0c0');
        $xmlWriter->writeAttribute('style:layout-grid-lines', '25199');
        $xmlWriter->writeAttribute('style:layout-grid-base-height', '0.423cm');
        $xmlWriter->writeAttribute('style:layout-grid-ruby-height', '0cm');
        $xmlWriter->writeAttribute('style:layout-grid-mode', 'none');
        $xmlWriter->writeAttribute('style:layout-grid-ruby-below', 'false');
        $xmlWriter->writeAttribute('style:layout-grid-print', 'false');
        $xmlWriter->writeAttribute('style:layout-grid-display', 'false');
        $xmlWriter->writeAttribute('style:layout-grid-base-width', '0.37cm');
        $xmlWriter->writeAttribute('style:layout-grid-snap-to', 'true');
        $xmlWriter->writeAttribute('style:footnote-max-height', '0cm');

        $xmlWriter->startElement('style:footnote-sep');
        $xmlWriter->writeAttribute('style:width', '0.018cm');
        $xmlWriter->writeAttribute('style:line-style', 'solid');
        $xmlWriter->writeAttribute('style:adjustment', 'left');
        $xmlWriter->writeAttribute('style:rel-width', '25%');
        $xmlWriter->writeAttribute('style:color', '#000000');
        $xmlWriter->endElement(); //style:footnote-sep

        $xmlWriter->endElement(); // style:page-layout-properties

        $xmlWriter->startElement('style:header-style');
        if ($topfactor < 1.0) {
            $xmlWriter->startElement('style:header-footer-properties');
            $xmlWriter->writeAttribute('fo:min-height', $mtop);
            $xmlWriter->writeAttribute('fo:margin-bottom', $mtop);
            $xmlWriter->writeAttribute('style:dynamic-spacing', 'true');
            $xmlWriter->endElement(); // style:header-footer-properties
        }
        $xmlWriter->endElement(); // style:header-style

        $xmlWriter->startElement('style:footer-style');
        if ($botfactor < 1.0) {
            $xmlWriter->startElement('style:header-footer-properties');
            $xmlWriter->writeAttribute('fo:min-height', $mbottom);
            $xmlWriter->writeAttribute('fo:margin-top', $mbottom);
            $xmlWriter->writeAttribute('style:dynamic-spacing', 'true');
            $xmlWriter->endElement(); // style:header-footer-properties
        }
        $xmlWriter->endElement(); // style:footer-style

        $xmlWriter->endElement(); // style:page-layout
    }

    /**
     * Write master style.
     */
    private function writeMaster(XMLWriter $xmlWriter): void
    {
        $xmlWriter->startElement('office:master-styles');

        $sections = $this->getParentWriter()->getPhpWord()->getSections();
        $countsects = count($sections);
        for ($i = 0; $i < $countsects; ++$i) {
            $iplus1 = $i + 1;
            $xmlWriter->startElement('style:master-page');
            $xmlWriter->writeAttribute('style:name', "Standard$iplus1");
            $xmlWriter->writeAttribute('style:page-layout-name', "Mpm$iplus1");
            // Multiple headers and footers probably not supported,
            //   and, even if they are, I'm not sure how,
            //   so quit after generating one.
            foreach ($sections[$i]->getHeaders() as $hdr) {
                $xmlWriter->startElement('style:header');
                foreach ($hdr->getElements() as $elem) {
                    $cl1 = get_class($elem);
                    $cl2 = str_replace('\\Element\\', '\\Writer\\ODText\\Element\\', $cl1);
                    if (class_exists($cl2)) {
                        $wtr = new $cl2($xmlWriter, $elem);
                        $wtr->write();
                    }
                }
                $xmlWriter->endElement(); // style:header

                break;
            }
            foreach ($sections[$i]->getFooters() as $hdr) {
                $xmlWriter->startElement('style:footer');
                foreach ($hdr->getElements() as $elem) {
                    $cl1 = get_class($elem);
                    $cl2 = str_replace('\\Element\\', '\\Writer\\ODText\\Element\\', $cl1);
                    if (class_exists($cl2)) {
                        $wtr = new $cl2($xmlWriter, $elem);
                        $wtr->write();
                    }
                }
                $xmlWriter->endElement(); // style:footer

                break;
            }
            $xmlWriter->endElement(); // style:master-page
        }
        $xmlWriter->endElement(); // office:master-styles
    }
}
