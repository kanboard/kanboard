<?php

namespace PhpOffice\PhpWord\Writer\EPub3\Element;

/**
 * Text element writer for EPub3.
 */
class Text extends AbstractElement
{
    /**
     * Write element.
     */
    public function write(): void
    {
        $xmlWriter = $this->getXmlWriter();
        $xmlWriter->setIndent(true);
        $xmlWriter->setIndentString('  ');
        $element = $this->getElement();
        if (!$element instanceof \PhpOffice\PhpWord\Element\Text) {
            return;
        }

        $fontStyle = $element->getFontStyle();
        $paragraphStyle = $element->getParagraphStyle();

        if (!$this->withoutP) {
            $xmlWriter->startElement('p');
            if (is_string($paragraphStyle) && $paragraphStyle !== '') {
                $xmlWriter->writeAttribute('class', $paragraphStyle);
            }
        }

        if (!empty($fontStyle)) {
            $xmlWriter->startElement('span');
            if (is_string($fontStyle)) {
                $xmlWriter->writeAttribute('class', $fontStyle);
            }
        }

        $xmlWriter->text($element->getText());

        if (!empty($fontStyle)) {
            $xmlWriter->endElement(); // span
        }

        if (!$this->withoutP) {
            $xmlWriter->endElement(); // p
        }
    }
}
