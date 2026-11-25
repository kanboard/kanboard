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

use PhpOffice\PhpWord\Element\AbstractContainer;
use PhpOffice\PhpWord\Element\Field;
use PhpOffice\PhpWord\Element\Image;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Element\TrackChange;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\XMLWriter;
use PhpOffice\PhpWord\Style;
use PhpOffice\PhpWord\Style\Font;
use PhpOffice\PhpWord\Style\Paragraph;
use PhpOffice\PhpWord\Style\Table as TableStyle;
use PhpOffice\PhpWord\Writer\ODText\Element\Container;
use PhpOffice\PhpWord\Writer\ODText\Style\Paragraph as ParagraphStyleWriter;

/**
 * ODText content part writer: content.xml.
 */
class Content extends AbstractPart
{
    /**
     * Auto style collection.
     *
     * Collect inline style information from section, image, and table elements
     *
     * @todo Merge font and paragraph styles
     *
     * @var array
     */
    private $autoStyles = ['Section' => [], 'Image' => [], 'Table' => []];

    private $imageParagraphStyles = [];

    /**
     * Write part.
     *
     * @return string
     */
    public function write()
    {
        $xmlWriter = $this->getXmlWriter();
        $phpWord = $this->getParentWriter()->getPhpWord();

        $this->getAutoStyles($phpWord);

        $xmlWriter->startDocument('1.0', 'UTF-8');
        $xmlWriter->startElement('office:document-content');
        $this->writeCommonRootAttributes($xmlWriter);
        $xmlWriter->writeAttribute('xmlns:xforms', 'http://www.w3.org/2002/xforms');
        $xmlWriter->writeAttribute('xmlns:xsd', 'http://www.w3.org/2001/XMLSchema');
        $xmlWriter->writeAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $xmlWriter->writeAttribute('xmlns:field', 'urn:openoffice:names:experimental:ooo-ms-interop:xmlns:field:1.0');
        $xmlWriter->writeAttribute('xmlns:formx', 'urn:openoffice:names:experimental:ooxml-odf-interop:xmlns:form:1.0');

        // Font declarations and automatic styles
        $this->writeFontFaces($xmlWriter); // office:font-face-decls
        $this->writeAutoStyles($xmlWriter); // office:automatic-styles

        // Body
        $xmlWriter->startElement('office:body');
        $xmlWriter->startElement('office:text');

        // Tracked changes declarations
        $trackedChanges = [];
        $sections = $phpWord->getSections();
        foreach ($sections as $section) {
            $this->collectTrackedChanges($section, $trackedChanges);
        }
        $xmlWriter->startElement('text:tracked-changes');
        foreach ($trackedChanges as $trackedElement) {
            $trackedChange = $trackedElement->getTrackChange();
            $xmlWriter->startElement('text:changed-region');
            $trackedChange->setElementId();
            $xmlWriter->writeAttribute('text:id', $trackedChange->getElementId());

            if (($trackedChange->getChangeType() == TrackChange::INSERTED)) {
                $xmlWriter->startElement('text:insertion');
            } elseif ($trackedChange->getChangeType() == TrackChange::DELETED) {
                $xmlWriter->startElement('text:deletion');
            }

            $xmlWriter->startElement('office:change-info');
            $xmlWriter->writeElement('dc:creator', $trackedChange->getAuthor());
            if ($trackedChange->getDate() != null) {
                $xmlWriter->writeElement('dc:date', $trackedChange->getDate()->format('Y-m-d\TH:i:s\Z'));
            }
            $xmlWriter->endElement(); // office:change-info
            if ($trackedChange->getChangeType() == TrackChange::DELETED) {
                $xmlWriter->writeElement('text:p', $trackedElement->getText());
            }

            $xmlWriter->endElement(); // text:insertion|text:deletion
            $xmlWriter->endElement(); // text:changed-region
        }
        $xmlWriter->endElement(); // text:tracked-changes

        // Sequence declarations
        $sequences = ['Illustration', 'Table', 'Text', 'Drawing'];
        $xmlWriter->startElement('text:sequence-decls');
        foreach ($sequences as $sequence) {
            $xmlWriter->startElement('text:sequence-decl');
            $xmlWriter->writeAttribute('text:display-outline-level', 0);
            $xmlWriter->writeAttribute('text:name', $sequence);
            $xmlWriter->endElement();
        }
        $xmlWriter->endElement(); // text:sequence-decl

        // Sections
        $sections = $phpWord->getSections();
        foreach ($sections as $section) {
            $name = 'Section' . $section->getSectionId();
            $xmlWriter->startElement('text:section');
            $xmlWriter->writeAttribute('text:name', $name);
            $xmlWriter->writeAttribute('text:style-name', $name);
            $xmlWriter->startElement('text:p');
            $xmlWriter->writeAttribute('text:style-name', 'SB' . $section->getSectionId());
            $xmlWriter->endElement();

            $containerWriter = new Container($xmlWriter, $section);
            $containerWriter->setPart($this);
            $containerWriter->write();

            $xmlWriter->endElement(); // text:section
        }

        $xmlWriter->endElement(); // office:text
        $xmlWriter->endElement(); // office:body

        $xmlWriter->endElement(); // office:document-content

        return $xmlWriter->getData();
    }

    /**
     * Write automatic styles other than fonts and paragraphs.
     *
     * @since 0.11.0
     */
    private function writeAutoStyles(XMLWriter $xmlWriter): void
    {
        $xmlWriter->startElement('office:automatic-styles');

        $this->writeTextStyles($xmlWriter);
        foreach ($this->autoStyles as $element => $styles) {
            $writerClass = 'PhpOffice\\PhpWord\\Writer\\ODText\\Style\\' . $element;
            foreach ($styles as $style) {
                /** @var \PhpOffice\PhpWord\Writer\ODText\Style\AbstractStyle $styleWriter Type hint */
                $styleWriter = new $writerClass($xmlWriter, $style);
                $styleWriter->write();
            }
        }

        $xmlWriter->endElement(); // office:automatic-styles
    }

    /**
     * Write automatic styles.
     */
    private function writeTextStyles(XMLWriter $xmlWriter): void
    {
        $styles = Style::getStyles();
        $paragraphStyleCount = 0;

        $style = new Paragraph();
        $style->setStyleName('PB');
        $style->setAuto();
        $styleWriter = new ParagraphStyleWriter($xmlWriter, $style);
        $styleWriter->write();

        $sects = $this->getParentWriter()->getPhpWord()->getSections();
        $countsects = count($sects);
        for ($i = 0; $i < $countsects; ++$i) {
            $iplus1 = $i + 1;
            $style = new Paragraph();
            $style->setStyleName("SB$iplus1");
            $style->setAuto();
            $pnstart = $sects[$i]->getStyle()->getPageNumberingStart();
            $style->setNumLevel($pnstart);
            $styleWriter = new ParagraphStyleWriter($xmlWriter, $style);
            $styleWriter->write();
        }

        foreach ($styles as $style) {
            $sty = (string) $style->getStyleName();
            if (substr($sty, 0, 8) === 'Heading_') {
                $style = new Paragraph();
                $style->setStyleName('HD' . substr($sty, 8));
                $style->setAuto();
                $styleWriter = new ParagraphStyleWriter($xmlWriter, $style);
                $styleWriter->write();
                $style = new Paragraph();
                $style->setStyleName('HE' . substr($sty, 8));
                $style->setAuto();
                $styleWriter = new ParagraphStyleWriter($xmlWriter, $style);
                $styleWriter->write();
            }
        }

        foreach ($styles as $style) {
            if ($style->isAuto() === true) {
                $styleClass = str_replace('\\Style\\', '\\Writer\\ODText\\Style\\', get_class($style));
                if (class_exists($styleClass)) {
                    /** @var \PhpOffice\PhpWord\Writer\ODText\Style\AbstractStyle $styleWriter Type hint */
                    $styleWriter = new $styleClass($xmlWriter, $style);
                    $styleWriter->write();
                }
                if ($style instanceof Paragraph) {
                    ++$paragraphStyleCount;
                }
            }
        }
        foreach ($this->imageParagraphStyles as $style) {
            $styleWriter = new ParagraphStyleWriter($xmlWriter, $style);
            $styleWriter->write();
        }
    }

    /**
     * Get automatic styles.
     */
    private function getAutoStyles(PhpWord $phpWord): void
    {
        $sections = $phpWord->getSections();
        $paragraphStyleCount = 0;
        $fontStyleCount = 0;
        foreach ($sections as $section) {
            $style = $section->getStyle();
            $style->setStyleName("Section{$section->getSectionId()}");
            $this->autoStyles['Section'][] = $style;
            $this->getContainerStyle($section, $paragraphStyleCount, $fontStyleCount);
        }
    }

    /**
     * Get all styles of each elements in container recursively.
     *
     * Table style can be null or string of the style name
     *
     * @param AbstractContainer $container
     * @param int $paragraphStyleCount
     * @param int $fontStyleCount
     *
     * @todo Simplify the logic
     */
    private function getContainerStyle($container, &$paragraphStyleCount, &$fontStyleCount): void
    {
        $elements = $container->getElements();
        foreach ($elements as $element) {
            if ($element instanceof TextRun) {
                $this->getElementStyleTextRun($element, $paragraphStyleCount);
                $this->getContainerStyle($element, $paragraphStyleCount, $fontStyleCount);
            } elseif ($element instanceof Text) {
                $this->getElementStyle($element, $paragraphStyleCount, $fontStyleCount);
            } elseif ($element instanceof Field) {
                $this->getElementStyleField($element, $fontStyleCount);
            } elseif ($element instanceof Image) {
                $style = $element->getStyle();
                $style->setStyleName('fr' . $element->getMediaIndex());
                $this->autoStyles['Image'][] = $style;
                $sty = new Paragraph();
                $sty->setStyleName('IM' . $element->getMediaIndex());
                $sty->setAuto();
                $sty->setAlignment($style->getAlignment());
                $this->imageParagraphStyles[] = $sty;
            } elseif ($element instanceof Table) {
                $style = $element->getStyle();
                if (is_string($style)) {
                    $style = Style::getStyle($style);
                }
                if ($style === null) {
                    $style = new TableStyle();
                }
                $style->setStyleName($element->getElementId());
                $style->setColumnWidths($element->findFirstDefinedCellWidths());
                $this->autoStyles['Table'][] = $style;
            }
        }
    }

    /**
     * Get style of individual element.
     *
     * @param Text $element
     * @param int $paragraphStyleCount
     * @param int $fontStyleCount
     */
    private function getElementStyle($element, &$paragraphStyleCount, &$fontStyleCount): void
    {
        $fontStyle = $element->getFontStyle();
        $paragraphStyle = $element->getParagraphStyle();
        $phpWord = $this->getParentWriter()->getPhpWord();

        if ($fontStyle instanceof Font) {
            // Font
            $name = $fontStyle->getStyleName();
            if (!$name) {
                ++$fontStyleCount;
                $style = $phpWord->addFontStyle("T{$fontStyleCount}", $fontStyle, null);
                $style->setAuto();
                $style->setParagraph(null);
                $element->setFontStyle("T{$fontStyleCount}");
            } else {
                $element->setFontStyle($name);
            }
        }
        if ($paragraphStyle instanceof Paragraph) {
            // Paragraph
            $name = $paragraphStyle->getStyleName();
            if (!$name) {
                ++$paragraphStyleCount;
                $style = $phpWord->addParagraphStyle("P{$paragraphStyleCount}", $paragraphStyle);
                $style->setAuto();
                $element->setParagraphStyle("P{$paragraphStyleCount}");
            } else {
                $element->setParagraphStyle($name);
            }
        } elseif ($paragraphStyle) {
            ++$paragraphStyleCount;
            $parstylename = "P$paragraphStyleCount" . "_$paragraphStyle";
            $style = $phpWord->addParagraphStyle($parstylename, $paragraphStyle);
            $style->setAuto();
            $element->setParagraphStyle($parstylename);
        }
    }

    /**
     * Get font style of individual field element.
     *
     * @param Field $element
     * @param int $fontStyleCount
     */
    private function getElementStyleField($element, &$fontStyleCount): void
    {
        $fontStyle = $element->getFontStyle();
        $phpWord = $this->getParentWriter()->getPhpWord();

        if ($fontStyle instanceof Font) {
            $name = $fontStyle->getStyleName();
            if (!$name) {
                ++$fontStyleCount;
                $style = $phpWord->addFontStyle("T{$fontStyleCount}", $fontStyle, null);
                $style->setAuto();
                $style->setParagraph(null);
                $element->setFontStyle("T{$fontStyleCount}");
            } else {
                $element->setFontStyle($name);
            }
        }
    }

    /**
     * Get style of individual element.
     *
     * @param TextRun $element
     * @param int $paragraphStyleCount
     */
    private function getElementStyleTextRun($element, &$paragraphStyleCount): void
    {
        $paragraphStyle = $element->getParagraphStyle();
        $phpWord = $this->getParentWriter()->getPhpWord();

        if ($paragraphStyle instanceof Paragraph) {
            // Paragraph
            $name = $paragraphStyle->getStyleName();
            if (!$name) {
                ++$paragraphStyleCount;
                $style = $phpWord->addParagraphStyle("P{$paragraphStyleCount}", $paragraphStyle);
                $style->setAuto();
                $element->setParagraphStyle("P{$paragraphStyleCount}");
            } else {
                $element->setParagraphStyle($name);
            }
        } elseif ($paragraphStyle) {
            ++$paragraphStyleCount;
            $parstylename = "P$paragraphStyleCount" . "_$paragraphStyle";
            $style = $phpWord->addParagraphStyle($parstylename, $paragraphStyle);
            $style->setAuto();
            $element->setParagraphStyle($parstylename);
        }
    }

    /**
     * Finds all tracked changes.
     *
     * @param \PhpOffice\PhpWord\Element\AbstractElement[] $trackedChanges
     */
    private function collectTrackedChanges(AbstractContainer $container, &$trackedChanges = []): void
    {
        $elements = $container->getElements();
        foreach ($elements as $element) {
            if ($element->getTrackChange() != null) {
                $trackedChanges[] = $element;
            }
            if (is_callable([$element, 'getElements'])) {
                $this->collectTrackedChanges($element, $trackedChanges);
            }
        }
    }
}
