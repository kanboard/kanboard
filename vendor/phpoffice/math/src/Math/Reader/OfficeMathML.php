<?php

namespace PhpOffice\Math\Reader;

use DOMDocument;
use DOMNode;
use DOMXPath;
use PhpOffice\Math\Element;
use PhpOffice\Math\Exception\InvalidInputException;
use PhpOffice\Math\Exception\NotImplementedException;
use PhpOffice\Math\Math;

class OfficeMathML implements ReaderInterface
{
    /** @var DOMDocument */
    protected $dom;

    /** @var Math */
    protected $math;

    /** @var DOMXPath */
    protected $xpath;

    /** @var string[] */
    protected $operators = ['+', '-', '/', '∗'];

    public function read(string $content): ?Math
    {
        $nsMath = 'xmlns:m="http://schemas.openxmlformats.org/officeDocument/2006/math"';
        $nsWord = 'xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"';

        $content = str_replace(
            $nsMath,
            $nsMath . ' ' . $nsWord,
            $content
        );

        $this->dom = new DOMDocument();
        $this->dom->loadXML($content);

        $this->math = new Math();
        $this->parseNode(null, $this->math);

        return $this->math;
    }

    /**
     * @see https://devblogs.microsoft.com/math-in-office/officemath/
     * @see https://learn.microsoft.com/fr-fr/archive/blogs/murrays/mathml-and-ecma-math-omml
     *
     * @param Math|Element\AbstractGroupElement $parent
     */
    protected function parseNode(?DOMNode $nodeRowElement, $parent): void
    {
        $this->xpath = new DOMXPath($this->dom);
        foreach ($this->xpath->query('*', $nodeRowElement) ?: [] as $nodeElement) {
            $element = $this->getElement($nodeElement);
            $parent->add($element);

            if ($element instanceof Element\AbstractGroupElement) {
                $this->parseNode($nodeElement, $element);
            }
        }
    }

    protected function getElement(DOMNode $nodeElement): Element\AbstractElement
    {
        switch ($nodeElement->nodeName) {
            case 'm:f':
                // Numerator
                $nodeNumerator = $this->xpath->query('m:num/m:r/m:t', $nodeElement);
                if ($nodeNumerator && $nodeNumerator->length == 1) {
                    $value = $nodeNumerator->item(0)->nodeValue;
                    if (is_numeric($value)) {
                        $numerator = new Element\Numeric(floatval($value));
                    } else {
                        $numerator = new Element\Identifier($value);
                    }
                } else {
                    throw new InvalidInputException(sprintf(
                        '%s : The tag `%s` has no numerator defined',
                        __METHOD__,
                        $nodeElement->nodeName
                    ));
                }
                // Denominator
                $nodeDenominator = $this->xpath->query('m:den/m:r/m:t', $nodeElement);
                if ($nodeDenominator && $nodeDenominator->length == 1) {
                    $value = $nodeDenominator->item(0)->nodeValue;
                    if (is_numeric($value)) {
                        $denominator = new Element\Numeric(floatval($value));
                    } else {
                        $denominator = new Element\Identifier($value);
                    }
                } else {
                    throw new InvalidInputException(sprintf(
                        '%s : The tag `%s` has no denominator defined',
                        __METHOD__,
                        $nodeElement->nodeName
                    ));
                }

                return new Element\Fraction($numerator, $denominator);
            case 'm:r':
                $nodeText = $this->xpath->query('m:t', $nodeElement);
                if ($nodeText && $nodeText->length == 1) {
                    $value = trim($nodeText->item(0)->nodeValue);
                    if (in_array($value, $this->operators)) {
                        return new Element\Operator($value);
                    }
                    if (is_numeric($value)) {
                        return new Element\Numeric(floatval($value));
                    }

                    return new Element\Identifier($value);
                }

                throw new InvalidInputException(sprintf(
                    '%s : The tag `%s` has no tag `m:t` defined',
                    __METHOD__,
                    $nodeElement->nodeName
                ));
            case 'm:oMath':
                return new Element\Row();
            default:
                throw new NotImplementedException(sprintf(
                    '%s : The tag `%s` is not implemented',
                    __METHOD__,
                    $nodeElement->nodeName
                ));
        }
    }
}
