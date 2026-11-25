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

use DateTime;

/**
 * Word2007 custom document properties part writer: docProps/custom.xml.
 *
 * @since 0.11.0
 */
class DocPropsCustom extends AbstractPart
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

        $xmlWriter->startDocument('1.0', 'UTF-8', 'yes');
        $xmlWriter->startElement('Properties');
        $xmlWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/officeDocument/2006/custom-properties');
        $xmlWriter->writeAttribute('xmlns:vt', 'http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes');

        $docProps = $phpWord->getDocInfo();
        $properties = $docProps->getCustomProperties();
        foreach ($properties as $key => $property) {
            $propertyValue = $docProps->getCustomPropertyValue($property);
            $propertyType = $docProps->getCustomPropertyType($property);

            $xmlWriter->startElement('property');
            $xmlWriter->writeAttribute('fmtid', '{D5CDD505-2E9C-101B-9397-08002B2CF9AE}');
            $xmlWriter->writeAttribute('pid', $key + 2);
            $xmlWriter->writeAttribute('name', $property);
            switch ($propertyType) {
                case 'i':
                    $xmlWriter->writeElement('vt:i4', $propertyValue);

                    break;
                case 'f':
                    $xmlWriter->writeElement('vt:r8', $propertyValue);

                    break;
                case 'b':
                    $xmlWriter->writeElement('vt:bool', ($propertyValue) ? 'true' : 'false');

                    break;
                case 'd':
                    if ($propertyValue instanceof DateTime) {
                        $xmlWriter->writeElement('vt:filetime', $propertyValue->format($this->dateFormat));
                    } else {
                        $xmlWriter->writeElement('vt:filetime', date($this->dateFormat, $propertyValue));
                    }

                    break;
                default:
                    $xmlWriter->writeElement('vt:lpwstr', $propertyValue);

                    break;
            }
            $xmlWriter->endElement(); // property
        }

        $xmlWriter->endElement(); // Properties

        return $xmlWriter->getData();
    }
}
