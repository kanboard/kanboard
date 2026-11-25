<?php

namespace PhpOffice\PhpWord\Writer\EPub3\Part;

use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\PhpWord;
use XMLWriter;

/**
 * Class for EPub3 content.xhtml part.
 */
class ContentXhtml extends AbstractPart
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

        return $xmlWriter;
    }

    /**
     * Write part content.
     */
    public function write(): string
    {
        if ($this->phpWord === null) {
            throw new \PhpOffice\PhpWord\Exception\Exception('No PhpWord assigned.');
        }

        $xmlWriter = $this->getXmlWriter();

        $xmlWriter->startDocument('1.0', 'UTF-8');
        $xmlWriter->startElement('html');
        $xmlWriter->writeAttribute('xmlns', 'http://www.w3.org/1999/xhtml');
        $xmlWriter->writeAttribute('xmlns:epub', 'http://www.idpf.org/2007/ops');
        $xmlWriter->startElement('head');
        $xmlWriter->writeElement('title', $this->phpWord->getDocInfo()->getTitle() ?: 'Untitled');
        $xmlWriter->endElement(); // head
        $xmlWriter->startElement('body');

        // Write sections content
        foreach ($this->phpWord->getSections() as $section) {
            $xmlWriter->startElement('div');
            $xmlWriter->writeAttribute('class', 'section');

            foreach ($section->getElements() as $element) {
                if ($element instanceof TextRun) {
                    $xmlWriter->startElement('p');
                    $this->writeTextRun($element, $xmlWriter);
                    $xmlWriter->endElement(); // p
                } elseif (method_exists($element, 'getText')) {
                    $text = $element->getText();
                    $xmlWriter->startElement('p');
                    if ($text instanceof TextRun) {
                        $this->writeTextRun($text, $xmlWriter);
                    } elseif ($text !== null) {
                        $xmlWriter->text((string) $text);
                    }
                    $xmlWriter->endElement(); // p
                }
            }

            $xmlWriter->endElement(); // div
        }

        $xmlWriter->endElement(); // body
        $xmlWriter->endElement(); // html

        return $xmlWriter->outputMemory(true);
    }

    protected function writeTextElement(\PhpOffice\PhpWord\Element\AbstractElement $textElement, XMLWriter $xmlWriter): void
    {
        if ($textElement instanceof Text) {
            $text = $textElement->getText();
            if ($text !== null) {
                $xmlWriter->text((string) $text);
            }
        } elseif (is_object($textElement) && method_exists($textElement, 'getText')) {
            $text = $textElement->getText();
            if ($text instanceof TextRun) {
                $this->writeTextRun($text, $xmlWriter);
            } elseif ($text !== null) {
                $xmlWriter->text((string) $text);
            }
        }
    }

    protected function writeTextRun(TextRun $textRun, XMLWriter $xmlWriter): void
    {
        foreach ($textRun->getElements() as $element) {
            $this->writeTextElement($element, $xmlWriter);
        }
    }
}
