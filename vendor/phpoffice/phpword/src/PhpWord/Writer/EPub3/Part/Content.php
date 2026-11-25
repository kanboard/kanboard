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

use PhpOffice\PhpWord\Exception\Exception;
use PhpOffice\PhpWord\PhpWord;
use XMLWriter;

/**
 * Class for EPub3 content part.
 */
class Content extends AbstractPart
{
    /**
     * PHPWord object.
     *
     * @var ?PhpWord
     */
    private $phpWord;

    /**
     * Constructor.
     */
    public function __construct(?PhpWord $phpWord = null)
    {
        $this->phpWord = $phpWord;
    }

    /**
     * Get XML Writer.
     *
     * @return XMLWriter
     */
    protected function getXmlWriter()
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
        if ($this->phpWord === null) {
            throw new Exception('No PhpWord assigned.');
        }

        $xmlWriter = $this->getXmlWriter();
        $docInfo = $this->phpWord->getDocInfo();

        // Write package
        $xmlWriter->startElement('package');
        $xmlWriter->writeAttribute('xmlns', 'http://www.idpf.org/2007/opf');
        $xmlWriter->writeAttribute('version', '3.0');
        $xmlWriter->writeAttribute('unique-identifier', 'book-id');
        $xmlWriter->writeAttribute('xml:lang', 'en');

        // Write metadata
        $xmlWriter->startElement('metadata');
        $xmlWriter->writeAttribute('xmlns:dc', 'http://purl.org/dc/elements/1.1/');
        $xmlWriter->writeAttribute('xmlns:opf', 'http://www.idpf.org/2007/opf');

        // Required elements
        $xmlWriter->startElement('dc:identifier');
        $xmlWriter->writeAttribute('id', 'book-id');
        $xmlWriter->text('book-id-' . uniqid());
        $xmlWriter->endElement();
        $xmlWriter->writeElement('dc:title', $docInfo->getTitle() ?: 'Untitled');
        $xmlWriter->writeElement('dc:language', 'en');

        // Required modified timestamp
        $xmlWriter->startElement('meta');
        $xmlWriter->writeAttribute('property', 'dcterms:modified');
        $xmlWriter->text(date('Y-m-d\TH:i:s\Z'));
        $xmlWriter->endElement();

        $xmlWriter->endElement(); // metadata

        // Write manifest
        $xmlWriter->startElement('manifest');

        // Add nav document (required)
        $xmlWriter->startElement('item');
        $xmlWriter->writeAttribute('id', 'nav');
        $xmlWriter->writeAttribute('href', 'nav.xhtml');
        $xmlWriter->writeAttribute('media-type', 'application/xhtml+xml');
        $xmlWriter->writeAttribute('properties', 'nav');
        $xmlWriter->endElement();

        // Add content document
        $xmlWriter->startElement('item');
        $xmlWriter->writeAttribute('id', 'content');
        $xmlWriter->writeAttribute('href', 'content.xhtml');
        $xmlWriter->writeAttribute('media-type', 'application/xhtml+xml');
        $xmlWriter->endElement();

        $xmlWriter->endElement(); // manifest

        // Write spine
        $xmlWriter->startElement('spine');
        $xmlWriter->startElement('itemref');
        $xmlWriter->writeAttribute('idref', 'content');
        $xmlWriter->endElement();
        $xmlWriter->endElement(); // spine

        $xmlWriter->endElement(); // package

        return $xmlWriter->outputMemory(true);
    }
}
