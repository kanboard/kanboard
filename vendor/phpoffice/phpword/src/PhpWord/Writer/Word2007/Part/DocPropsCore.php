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

/**
 * Word2007 core document properties part writer: docProps/core.xml.
 *
 * @since 0.11.0
 */
class DocPropsCore extends AbstractPart
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
        $schema = 'http://schemas.openxmlformats.org/package/2006/metadata/core-properties';

        $xmlWriter->startDocument('1.0', 'UTF-8', 'yes');
        $xmlWriter->startElement('cp:coreProperties');
        $xmlWriter->writeAttribute('xmlns:cp', $schema);
        $xmlWriter->writeAttribute('xmlns:dc', 'http://purl.org/dc/elements/1.1/');
        $xmlWriter->writeAttribute('xmlns:dcterms', 'http://purl.org/dc/terms/');
        $xmlWriter->writeAttribute('xmlns:dcmitype', 'http://purl.org/dc/dcmitype/');
        $xmlWriter->writeAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');

        $xmlWriter->writeElement('dc:creator', $phpWord->getDocInfo()->getCreator());
        $xmlWriter->writeElement('dc:title', $phpWord->getDocInfo()->getTitle());
        $xmlWriter->writeElement('dc:description', $phpWord->getDocInfo()->getDescription());
        $xmlWriter->writeElement('dc:subject', $phpWord->getDocInfo()->getSubject());
        $xmlWriter->writeElement('cp:keywords', $phpWord->getDocInfo()->getKeywords());
        $xmlWriter->writeElement('cp:category', $phpWord->getDocInfo()->getCategory());
        $xmlWriter->writeElement('cp:lastModifiedBy', $phpWord->getDocInfo()->getLastModifiedBy());

        // dcterms:created
        $xmlWriter->startElement('dcterms:created');
        $xmlWriter->writeAttribute('xsi:type', 'dcterms:W3CDTF');
        $xmlWriter->text(date($this->dateFormat, $phpWord->getDocInfo()->getCreated()));
        $xmlWriter->endElement();

        // dcterms:modified
        $xmlWriter->startElement('dcterms:modified');
        $xmlWriter->writeAttribute('xsi:type', 'dcterms:W3CDTF');
        $xmlWriter->text(date($this->dateFormat, $phpWord->getDocInfo()->getModified()));
        $xmlWriter->endElement();

        $xmlWriter->endElement(); // cp:coreProperties

        return $xmlWriter->getData();
    }
}
