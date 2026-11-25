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

use PhpOffice\PhpWord\Shared\XMLWriter;

/**
 * ODText meta part writer: meta.xml.
 *
 * @since 0.11.0
 */
class Meta extends AbstractPart
{
    /**
     * Write part.
     *
     * @return string
     */
    public function write()
    {
        $phpWord = $this->getParentWriter()->getPhpWord();
        $docProps = $phpWord->getDocInfo();
        $xmlWriter = $this->getXmlWriter();

        $xmlWriter->startDocument('1.0', 'UTF-8');
        $xmlWriter->startElement('office:document-meta');
        $xmlWriter->writeAttribute('office:version', '1.2');
        $xmlWriter->writeAttribute('xmlns:office', 'urn:oasis:names:tc:opendocument:xmlns:office:1.0');
        $xmlWriter->writeAttribute('xmlns:xlink', 'http://www.w3.org/1999/xlink');
        $xmlWriter->writeAttribute('xmlns:dc', 'http://purl.org/dc/elements/1.1/');
        $xmlWriter->writeAttribute('xmlns:meta', 'urn:oasis:names:tc:opendocument:xmlns:meta:1.0');
        $xmlWriter->writeAttribute('xmlns:ooo', 'http://openoffice.org/2004/office');
        $xmlWriter->writeAttribute('xmlns:grddl', 'http://www.w3.org/2003/g/data-view#');
        $xmlWriter->startElement('office:meta');

        // Core properties
        $xmlWriter->writeElement('dc:title', $docProps->getTitle());
        $xmlWriter->writeElement('dc:subject', $docProps->getSubject());
        $xmlWriter->writeElement('dc:description', $docProps->getDescription());
        $xmlWriter->writeElement('dc:creator', $docProps->getLastModifiedBy());
        $xmlWriter->writeElement('dc:date', gmdate($this->dateFormat, $docProps->getModified()));

        // Extended properties
        $xmlWriter->writeElement('meta:generator', 'PHPWord');
        $xmlWriter->writeElement('meta:initial-creator', $docProps->getCreator());
        $xmlWriter->writeElement('meta:creation-date', gmdate($this->dateFormat, $docProps->getCreated()));
        $xmlWriter->writeElement('meta:keyword', $docProps->getKeywords());

        // Category, company, and manager are put in meta namespace
        $properties = ['Category', 'Company', 'Manager'];
        foreach ($properties as $property) {
            $method = "get{$property}";
            if ($docProps->$method() !== null) {
                $this->writeCustomProperty($xmlWriter, $property, $docProps->$method());
            }
        }

        // Other custom properties
        // @todo Check type. Currently all assumed as string
        foreach ($docProps->getCustomProperties() as $property) {
            $value = $docProps->getCustomPropertyValue($property);
            $this->writeCustomProperty($xmlWriter, $property, $value);
        }

        $xmlWriter->endElement(); // office:meta
        $xmlWriter->endElement(); // office:document-meta

        return $xmlWriter->getData();
    }

    /**
     * Write individual property.
     *
     * @param string $property
     * @param string $value
     *
     * @todo Handle other `$type`: double|date|dateTime|duration|boolean (4th arguments)
     */
    private function writeCustomProperty(XMLWriter $xmlWriter, $property, $value): void
    {
        $xmlWriter->startElement('meta:user-defined');
        $xmlWriter->writeAttribute('meta:name', $property);
        // if ($type !== null) {
        //     $xmlWriter->writeAttribute('meta:value-type', $type);
        // }
        $this->writeText($value);
        $xmlWriter->endElement(); // meta:user-defined
    }
}
