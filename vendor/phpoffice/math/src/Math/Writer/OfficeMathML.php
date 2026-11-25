<?php

namespace PhpOffice\Math\Writer;

use PhpOffice\Math\Element;
use PhpOffice\Math\Exception\NotImplementedException;
use PhpOffice\Math\Math;
use XMLWriter;

class OfficeMathML implements WriterInterface
{
    /** @var XMLWriter */
    private $output;

    /**
     * @param Math $math
     *
     * @return string
     */
    public function write(Math $math): string
    {
        $this->output = new XMLWriter();
        $this->output->openMemory();
        $this->output->startElement('m:oMathPara');
        $this->output->writeAttribute('xmlns:m', 'http://schemas.openxmlformats.org/officeDocument/2006/math');
        $this->output->startElement('m:oMath');

        foreach ($math->getElements() as $element) {
            $this->writeElementItem($element);
        }

        $this->output->endElement();
        $this->output->endElement();

        return $this->output->outputMemory();
    }

    protected function writeElementItem(Element\AbstractElement $element): void
    {
        // Element\Row
        if ($element instanceof Element\Row) {
            foreach ($element->getElements() as $childElement) {
                $this->writeElementItem($childElement);
            }

            return;
        }

        // Element\Fraction
        if ($element instanceof Element\Fraction) {
            $this->output->startElement($this->getElementTagName($element));
            $this->output->startElement('m:num');
            $this->writeElementItem($element->getNumerator());
            $this->output->endElement();
            $this->output->startElement('m:den');
            $this->writeElementItem($element->getDenominator());
            $this->output->endElement();
            $this->output->endElement();

            return;
        }

        if ($element instanceof Element\Identifier
          || $element instanceof Element\Numeric
          || $element instanceof Element\Operator) {
            $this->output->startElement('m:r');
            $this->output->startElement('m:t');
            $this->output->text((string) $element->getValue());
            $this->output->endElement();
            $this->output->endElement();

            return;
        }

        // Check if managed
        $this->getElementTagName($element);
    }

    protected function getElementTagName(Element\AbstractElement $element): string
    {
        // Group
        if ($element instanceof Element\AbstractGroupElement) {
            /*
            throw new NotImplementedException(sprintf(
                '%s : The element of the class `%s` has no tag name',
                __METHOD__,
                get_class($element)
            ));
            */
        }

        if ($element instanceof Element\Fraction) {
            return 'm:f';
        }

        throw new NotImplementedException(sprintf(
            '%s : The element of the class `%s` has no tag name',
            __METHOD__,
            get_class($element)
        ));
    }
}
