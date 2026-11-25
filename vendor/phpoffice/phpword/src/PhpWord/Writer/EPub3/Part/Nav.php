<?php

namespace PhpOffice\PhpWord\Writer\EPub3\Part;

use XMLWriter;

class Nav extends AbstractPart
{
    protected function getXmlWriter(): XMLWriter
    {
        $xmlWriter = new XMLWriter();
        $xmlWriter->openMemory();

        return $xmlWriter;
    }

    public function write(): string
    {
        $xmlWriter = $this->getXmlWriter();

        $xmlWriter->startDocument('1.0', 'UTF-8');
        $xmlWriter->startElement('html');
        $xmlWriter->writeAttribute('xmlns', 'http://www.w3.org/1999/xhtml');
        $xmlWriter->writeAttribute('xmlns:epub', 'http://www.idpf.org/2007/ops');

        $xmlWriter->startElement('head');
        $xmlWriter->writeElement('title', 'Navigation');
        $xmlWriter->endElement(); // head

        $xmlWriter->startElement('body');
        $xmlWriter->startElement('nav');
        $xmlWriter->writeAttribute('epub:type', 'toc');
        $xmlWriter->writeAttribute('id', 'toc');

        // Add navigation items here if needed
        $xmlWriter->writeElement('h1', 'Table of Contents');
        $xmlWriter->startElement('ol');
        // Add at least one list item to satisfy EPUB 3.3 requirements
        $xmlWriter->startElement('li');
        $xmlWriter->startElement('a');
        $xmlWriter->writeAttribute('href', 'content.xhtml');
        $xmlWriter->text('Content');
        $xmlWriter->endElement(); // a
        $xmlWriter->endElement(); // li
        $xmlWriter->endElement(); // ol

        $xmlWriter->endElement(); // nav
        $xmlWriter->endElement(); // body
        $xmlWriter->endElement(); // html

        return $xmlWriter->outputMemory();
    }
}
