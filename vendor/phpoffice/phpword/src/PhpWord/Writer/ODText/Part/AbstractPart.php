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

use PhpOffice\PhpWord\Element\AbstractElement;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\Shared\XMLWriter;
use PhpOffice\PhpWord\Style;
use PhpOffice\PhpWord\Style\Font;
use PhpOffice\PhpWord\Writer\Word2007\Part\AbstractPart as Word2007AbstractPart;

/**
 * ODText writer part abstract.
 */
abstract class AbstractPart extends Word2007AbstractPart
{
    /**
     * @var AbstractElement[]
     */
    protected $objects = [];

    /**
     * @var string Date format
     */
    protected $dateFormat = 'Y-m-d\TH:i:s.000';

    /**
     * Write common root attributes.
     */
    protected function writeCommonRootAttributes(XMLWriter $xmlWriter): void
    {
        $xmlWriter->writeAttribute('office:version', '1.2');
        $xmlWriter->writeAttribute('xmlns:office', 'urn:oasis:names:tc:opendocument:xmlns:office:1.0');
        $xmlWriter->writeAttribute('xmlns:style', 'urn:oasis:names:tc:opendocument:xmlns:style:1.0');
        $xmlWriter->writeAttribute('xmlns:text', 'urn:oasis:names:tc:opendocument:xmlns:text:1.0');
        $xmlWriter->writeAttribute('xmlns:table', 'urn:oasis:names:tc:opendocument:xmlns:table:1.0');
        $xmlWriter->writeAttribute('xmlns:draw', 'urn:oasis:names:tc:opendocument:xmlns:drawing:1.0');
        $xmlWriter->writeAttribute('xmlns:fo', 'urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0');
        $xmlWriter->writeAttribute('xmlns:xlink', 'http://www.w3.org/1999/xlink');
        $xmlWriter->writeAttribute('xmlns:dc', 'http://purl.org/dc/elements/1.1/');
        $xmlWriter->writeAttribute('xmlns:meta', 'urn:oasis:names:tc:opendocument:xmlns:meta:1.0');
        $xmlWriter->writeAttribute('xmlns:number', 'urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0');
        $xmlWriter->writeAttribute('xmlns:svg', 'urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0');
        $xmlWriter->writeAttribute('xmlns:chart', 'urn:oasis:names:tc:opendocument:xmlns:chart:1.0');
        $xmlWriter->writeAttribute('xmlns:dr3d', 'urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0');
        $xmlWriter->writeAttribute('xmlns:math', 'http://www.w3.org/1998/Math/MathML');
        $xmlWriter->writeAttribute('xmlns:form', 'urn:oasis:names:tc:opendocument:xmlns:form:1.0');
        $xmlWriter->writeAttribute('xmlns:script', 'urn:oasis:names:tc:opendocument:xmlns:script:1.0');
        $xmlWriter->writeAttribute('xmlns:ooo', 'http://openoffice.org/2004/office');
        $xmlWriter->writeAttribute('xmlns:ooow', 'http://openoffice.org/2004/writer');
        $xmlWriter->writeAttribute('xmlns:oooc', 'http://openoffice.org/2004/calc');
        $xmlWriter->writeAttribute('xmlns:dom', 'http://www.w3.org/2001/xml-events');
        $xmlWriter->writeAttribute('xmlns:rpt', 'http://openoffice.org/2005/report');
        $xmlWriter->writeAttribute('xmlns:of', 'urn:oasis:names:tc:opendocument:xmlns:of:1.2');
        $xmlWriter->writeAttribute('xmlns:xhtml', 'http://www.w3.org/1999/xhtml');
        $xmlWriter->writeAttribute('xmlns:grddl', 'http://www.w3.org/2003/g/data-view#');
        $xmlWriter->writeAttribute('xmlns:tableooo', 'http://openoffice.org/2009/table');
        $xmlWriter->writeAttribute('xmlns:css3t', 'http://www.w3.org/TR/css3-text/');
    }

    /**
     * Write font faces declaration.
     */
    protected function writeFontFaces(XMLWriter $xmlWriter): void
    {
        $xmlWriter->startElement('office:font-face-decls');
        $fontTable = [];
        $styles = Style::getStyles();
        $numFonts = 0;
        if (count($styles) > 0) {
            foreach ($styles as $style) {
                // Font
                if ($style instanceof Font) {
                    ++$numFonts;
                    $name = $style->getName();
                    if (!in_array($name, $fontTable)) {
                        $fontTable[] = $name;

                        // style:font-face
                        $xmlWriter->startElement('style:font-face');
                        $xmlWriter->writeAttribute('style:name', $name);
                        $xmlWriter->writeAttribute('svg:font-family', $name);
                        $xmlWriter->endElement();
                    }
                }
            }
        }
        if (!in_array(Settings::getDefaultFontName(), $fontTable)) {
            $xmlWriter->startElement('style:font-face');
            $xmlWriter->writeAttribute('style:name', Settings::getDefaultFontName());
            $xmlWriter->writeAttribute('svg:font-family', Settings::getDefaultFontName());
            $xmlWriter->endElement();
        }
        $xmlWriter->endElement();
    }

    public function addObject(AbstractElement $object): int
    {
        $this->objects[] = $object;

        return count($this->objects) - 1;
    }

    /**
     * @param AbstractElement[] $objects
     */
    public function setObjects(array $objects): self
    {
        $this->objects = $objects;

        return $this;
    }

    /**
     * @return AbstractElement[]
     */
    public function getObjects(): array
    {
        return $this->objects;
    }
}
