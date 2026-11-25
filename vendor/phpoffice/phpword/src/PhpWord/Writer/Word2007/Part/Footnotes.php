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

use PhpOffice\PhpWord\Element\Footnote;
use PhpOffice\PhpWord\Shared\XMLWriter;
use PhpOffice\PhpWord\Writer\Word2007\Element\Container;
use PhpOffice\PhpWord\Writer\Word2007\Style\Paragraph as ParagraphStyleWriter;

/**
 * Word2007 footnotes part writer: word/(footnotes|endnotes).xml.
 */
class Footnotes extends AbstractPart
{
    /**
     * Name of XML root element.
     *
     * @var string
     */
    protected $rootNode = 'w:footnotes';

    /**
     * Name of XML node element.
     *
     * @var string
     */
    protected $elementNode = 'w:footnote';

    /**
     * Name of XML reference element.
     *
     * @var string
     */
    protected $refNode = 'w:footnoteRef';

    /**
     * Reference style name.
     *
     * @var string
     */
    protected $refStyle = 'FootnoteReference';

    /**
     * Footnotes/endnotes collection to be written.
     *
     * @var \PhpOffice\PhpWord\Collection\Endnotes|\PhpOffice\PhpWord\Collection\Footnotes
     */
    protected $elements;

    /**
     * Write part.
     *
     * @return string
     */
    public function write()
    {
        $xmlWriter = $this->getXmlWriter();
        $drawingSchema = 'http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing';

        $xmlWriter->startDocument('1.0', 'UTF-8', 'yes');
        $xmlWriter->startElement($this->rootNode);
        $xmlWriter->writeAttribute('xmlns:ve', 'http://schemas.openxmlformats.org/markup-compatibility/2006');
        $xmlWriter->writeAttribute('xmlns:o', 'urn:schemas-microsoft-com:office:office');
        $xmlWriter->writeAttribute('xmlns:r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');
        $xmlWriter->writeAttribute('xmlns:m', 'http://schemas.openxmlformats.org/officeDocument/2006/math');
        $xmlWriter->writeAttribute('xmlns:v', 'urn:schemas-microsoft-com:vml');
        $xmlWriter->writeAttribute('xmlns:wp', $drawingSchema);
        $xmlWriter->writeAttribute('xmlns:w10', 'urn:schemas-microsoft-com:office:word');
        $xmlWriter->writeAttribute('xmlns:w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
        $xmlWriter->writeAttribute('xmlns:wne', 'http://schemas.microsoft.com/office/word/2006/wordml');

        // Separator and continuation separator
        $xmlWriter->startElement($this->elementNode);
        $xmlWriter->writeAttribute('w:id', -1);
        $xmlWriter->writeAttribute('w:type', 'separator');
        $xmlWriter->startElement('w:p');
        $xmlWriter->startElement('w:r');
        $xmlWriter->startElement('w:separator');
        $xmlWriter->endElement(); // w:separator
        $xmlWriter->endElement(); // w:r
        $xmlWriter->endElement(); // w:p
        $xmlWriter->endElement(); // $this->elementNode
        $xmlWriter->startElement($this->elementNode);
        $xmlWriter->writeAttribute('w:id', 0);
        $xmlWriter->writeAttribute('w:type', 'continuationSeparator');
        $xmlWriter->startElement('w:p');
        $xmlWriter->startElement('w:r');
        $xmlWriter->startElement('w:continuationSeparator');
        $xmlWriter->endElement(); // w:continuationSeparator
        $xmlWriter->endElement(); // w:r
        $xmlWriter->endElement(); // w:p
        $xmlWriter->endElement(); // $this->elementNode

        /** @var array $elements Type hint */
        $elements = $this->elements;
        foreach ($elements as $element) {
            if ($element instanceof Footnote) {
                $this->writeNote($xmlWriter, $element);
            }
        }

        $xmlWriter->endElement(); // $this->rootNode

        return $xmlWriter->getData();
    }

    /**
     * Set element.
     *
     * @param \PhpOffice\PhpWord\Collection\Endnotes|\PhpOffice\PhpWord\Collection\Footnotes $elements
     *
     * @return self
     */
    public function setElements($elements)
    {
        $this->elements = $elements;

        return $this;
    }

    /**
     * Write note item.
     *
     * @param Footnote|\PhpOffice\PhpWord\Element\Endnote $element
     */
    protected function writeNote(XMLWriter $xmlWriter, $element): void
    {
        $xmlWriter->startElement($this->elementNode);
        $xmlWriter->writeAttribute('w:id', $element->getRelationId() + 1);
        $xmlWriter->startElement('w:p');

        // Paragraph style
        $styleWriter = new ParagraphStyleWriter($xmlWriter, $element->getParagraphStyle());
        $styleWriter->setIsInline(true);
        $styleWriter->write();

        // Reference symbol
        $xmlWriter->startElement('w:r');
        $xmlWriter->startElement('w:rPr');
        $xmlWriter->startElement('w:rStyle');
        $xmlWriter->writeAttribute('w:val', $this->refStyle);
        $xmlWriter->endElement(); // w:rStyle
        $xmlWriter->endElement(); // w:rPr
        $xmlWriter->writeElement($this->refNode);
        $xmlWriter->endElement(); // w:r

        // Empty space after refence symbol
        $xmlWriter->startElement('w:r');
        $xmlWriter->startElement('w:t');
        $xmlWriter->writeAttribute('xml:space', 'preserve');
        $xmlWriter->text(' ');
        $xmlWriter->endElement(); // w:t
        $xmlWriter->endElement(); // w:r

        $containerWriter = new Container($xmlWriter, $element);
        $containerWriter->write();

        $xmlWriter->endElement(); // w:p
        $xmlWriter->endElement(); // $this->elementNode
    }
}
