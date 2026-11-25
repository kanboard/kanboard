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
 * Word2007 extended document properties part writer: docProps/app.xml.
 *
 * @since 0.11.0
 */
class DocPropsApp extends AbstractPart
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
        $schema = 'http://schemas.openxmlformats.org/officeDocument/2006/extended-properties';

        $xmlWriter->startDocument('1.0', 'UTF-8', 'yes');
        $xmlWriter->startElement('Properties');
        $xmlWriter->writeAttribute('xmlns', $schema);
        $xmlWriter->writeAttribute('xmlns:vt', 'http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes');

        $xmlWriter->writeElement('Application', 'PHPWord');
        $xmlWriter->writeElement('Company', $phpWord->getDocInfo()->getCompany());
        $xmlWriter->writeElement('Manager', $phpWord->getDocInfo()->getManager());

        $xmlWriter->endElement(); // Properties

        return $xmlWriter->getData();
    }
}
