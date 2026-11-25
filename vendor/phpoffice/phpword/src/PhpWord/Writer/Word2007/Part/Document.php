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

namespace PhpOffice\PhpWord\Writer\Word2007\Part;

use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\Shared\XMLWriter;
use PhpOffice\PhpWord\Writer\Word2007\Element\Container;
use PhpOffice\PhpWord\Writer\Word2007\Style\Section as SectionStyleWriter;

/**
 * Word2007 document part writer: word/document.xml.
 */
class Document extends AbstractPart
{
    /**
     * Write part.
     *
     * @return string
     */
    public function write()
    {
        $phpWord = $this->getParentWriter()->getPhpWord();
        $xmlWriter = $this->getXmlWriter();

        $sections = $phpWord->getSections();
        $sectionCount = count($sections);
        $currentSection = 0;
        $drawingSchema = 'http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing';

        $xmlWriter->startDocument('1.0', 'UTF-8', 'yes');
        $xmlWriter->startElement('w:document');
        $xmlWriter->writeAttribute('xmlns:ve', 'http://schemas.openxmlformats.org/markup-compatibility/2006');
        $xmlWriter->writeAttribute('xmlns:o', 'urn:schemas-microsoft-com:office:office');
        $xmlWriter->writeAttribute('xmlns:r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');
        $xmlWriter->writeAttribute('xmlns:m', 'http://schemas.openxmlformats.org/officeDocument/2006/math');
        $xmlWriter->writeAttribute('xmlns:v', 'urn:schemas-microsoft-com:vml');
        $xmlWriter->writeAttribute('xmlns:wp', $drawingSchema);
        $xmlWriter->writeAttribute('xmlns:w10', 'urn:schemas-microsoft-com:office:word');
        $xmlWriter->writeAttribute('xmlns:w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
        $xmlWriter->writeAttribute('xmlns:wne', 'http://schemas.microsoft.com/office/word/2006/wordml');

        $xmlWriter->startElement('w:body');

        if ($sectionCount > 0) {
            foreach ($sections as $section) {
                ++$currentSection;

                $containerWriter = new Container($xmlWriter, $section);
                $containerWriter->write();

                if ($currentSection == $sectionCount) {
                    $this->writeSectionSettings($xmlWriter, $section);
                } else {
                    $this->writeSection($xmlWriter, $section);
                }
            }
        }

        $xmlWriter->endElement(); // w:body
        $xmlWriter->endElement(); // w:document

        return $xmlWriter->getData();
    }

    /**
     * Write begin section.
     */
    private function writeSection(XMLWriter $xmlWriter, Section $section): void
    {
        $xmlWriter->startElement('w:p');
        $xmlWriter->startElement('w:pPr');
        $this->writeSectionSettings($xmlWriter, $section);
        $xmlWriter->endElement();
        $xmlWriter->endElement();
    }

    /**
     * Write end section.
     */
    private function writeSectionSettings(XMLWriter $xmlWriter, Section $section): void
    {
        $xmlWriter->startElement('w:sectPr');

        // Header reference
        foreach ($section->getHeaders() as $header) {
            $rId = $header->getRelationId();
            $xmlWriter->startElement('w:headerReference');
            $xmlWriter->writeAttribute('w:type', $header->getType());
            $xmlWriter->writeAttribute('r:id', 'rId' . $rId);
            $xmlWriter->endElement();
        }

        // Footer reference
        foreach ($section->getFooters() as $footer) {
            $rId = $footer->getRelationId();
            $xmlWriter->startElement('w:footerReference');
            $xmlWriter->writeAttribute('w:type', $footer->getType());
            $xmlWriter->writeAttribute('r:id', 'rId' . $rId);
            $xmlWriter->endElement();
        }

        // Different first page
        if ($section->hasDifferentFirstPage()) {
            $xmlWriter->startElement('w:titlePg');
            $xmlWriter->endElement();
        }

        // Footnote properties
        if ($section->getFootnoteProperties() !== null) {
            $xmlWriter->startElement('w:footnotePr');
            if ($section->getFootnoteProperties()->getPos() != null) {
                $xmlWriter->startElement('w:pos');
                $xmlWriter->writeAttribute('w:val', $section->getFootnoteProperties()->getPos());
                $xmlWriter->endElement();
            }
            if ($section->getFootnoteProperties()->getNumFmt() != null) {
                $xmlWriter->startElement('w:numFmt');
                $xmlWriter->writeAttribute('w:val', $section->getFootnoteProperties()->getNumFmt());
                $xmlWriter->endElement();
            }
            if ($section->getFootnoteProperties()->getNumStart() != null) {
                $xmlWriter->startElement('w:numStart');
                $xmlWriter->writeAttribute('w:val', $section->getFootnoteProperties()->getNumStart());
                $xmlWriter->endElement();
            }
            if ($section->getFootnoteProperties()->getNumRestart() != null) {
                $xmlWriter->startElement('w:numRestart');
                $xmlWriter->writeAttribute('w:val', $section->getFootnoteProperties()->getNumRestart());
                $xmlWriter->endElement();
            }
            $xmlWriter->endElement();
        }

        // Section settings
        $styleWriter = new SectionStyleWriter($xmlWriter, $section->getStyle());
        $styleWriter->write();

        $xmlWriter->endElement(); // w:sectPr
    }
}
