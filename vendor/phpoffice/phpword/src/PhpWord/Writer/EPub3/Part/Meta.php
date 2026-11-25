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

namespace PhpOffice\PhpWord\Writer\EPub3\Part;

use PhpOffice\PhpWord\Shared\XMLWriter;

/**
 * Class for EPub3 metadata part.
 */
class Meta extends AbstractPart
{
    /**
     * Get XML Writer.
     */
    protected function getXmlWriter(): XMLWriter
    {
        $xmlWriter = new XMLWriter();
        $xmlWriter->openMemory();
        $xmlWriter->startDocument('1.0', 'UTF-8');

        return $xmlWriter;
    }

    /**
     * Write part content.
     */
    public function write(): string
    {
        $xmlWriter = $this->getXmlWriter();

        $xmlWriter->startElement('metadata');
        $xmlWriter->writeAttribute('xmlns', 'http://www.idpf.org/2007/opf');
        $xmlWriter->writeAttribute('xmlns:dc', 'http://purl.org/dc/elements/1.1/');

        // Write basic metadata
        $title = $this->getParentWriter()->getPhpWord()->getDocInfo()->getTitle() ?: 'Sample EPub3 Document';
        $xmlWriter->writeRaw('<dc:title>' . htmlspecialchars($title, ENT_QUOTES) . '</dc:title>');
        $xmlWriter->writeElement('dc:language', 'en');
        $xmlWriter->writeElement('dc:identifier', 'urn:uuid:12345');
        $xmlWriter->writeAttribute('id', 'bookid');

        // Write document info if available
        $docInfo = $this->getParentWriter()->getPhpWord()->getDocInfo();
        if ($docInfo->getCreator()) {
            $xmlWriter->writeElement('dc:creator', $docInfo->getCreator());
        }

        // Write modification date
        $xmlWriter->startElement('meta');
        $xmlWriter->writeAttribute('property', 'dcterms:modified');
        $xmlWriter->text('2023-01-01T00:00:00Z');
        $xmlWriter->endElement();

        $xmlWriter->endElement(); // metadata

        return $xmlWriter->getData();
    }
}
