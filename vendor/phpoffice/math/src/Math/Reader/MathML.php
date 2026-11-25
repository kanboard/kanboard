<?php

namespace PhpOffice\Math\Reader;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;
use PhpOffice\Math\Element;
use PhpOffice\Math\Exception\InvalidInputException;
use PhpOffice\Math\Exception\NotImplementedException;
use PhpOffice\Math\Math;
use PhpOffice\Math\Reader\Security\XmlScanner;

class MathML implements ReaderInterface
{
    /** @var Math */
    private $math;

    /** @var DOMDocument */
    private $dom;

    /** @var DOMXPath */
    private $xpath;

    /** @var XmlScanner */
    private $xmlScanner;

    public function __construct()
    {
        $this->xmlScanner = XmlScanner::getInstance();
    }

    public function read(string $content): ?Math
    {
        $content = $this->xmlScanner->scan($content);
        $content = str_replace(
            [
                '&InvisibleTimes;',
            ],
            [
                '<mchar name="InvisibleTimes"/>',
            ],
            $content
        );

        $this->dom = new DOMDocument();
        $this->dom->loadXML($content);

        $this->math = new Math();
        $this->parseNode(null, $this->math);

        return $this->math;
    }

    /**
     * @param Math|Element\AbstractGroupElement $parent
     */
    protected function parseNode(?DOMNode $nodeRowElement, $parent): void
    {
        $this->xpath = new DOMXPath($this->dom);
        foreach ($this->xpath->query('*', $nodeRowElement) ?: [] as $nodeElement) {
            if ($parent instanceof Element\Semantics
                && $nodeElement instanceof DOMElement
                && $nodeElement->nodeName == 'annotation') {
                $parent->addAnnotation(
                    $nodeElement->getAttribute('encoding'),
                    trim($nodeElement->nodeValue)
                );

                continue;
            }

            $parent->add($this->getElement($nodeElement));
        }
    }

    protected function getElement(DOMNode $nodeElement): Element\AbstractElement
    {
        $nodeValue = trim($nodeElement->nodeValue);
        switch ($nodeElement->nodeName) {
            case 'mfrac':
                $nodeList = $this->xpath->query('*', $nodeElement);
                if ($nodeList && $nodeList->length == 2) {
                    return new Element\Fraction(
                        $this->getElement($nodeList->item(0)),
                        $this->getElement($nodeList->item(1))
                    );
                }

                throw new InvalidInputException(sprintf(
                    '%s : The tag `%s` has not two subelements',
                    __METHOD__,
                    $nodeElement->nodeName
                ));
            case 'mi':
                return new Element\Identifier($nodeValue);
            case 'mn':
                return new Element\Numeric(floatval($nodeValue));
            case 'mo':
                if (empty($nodeValue)) {
                    $nodeList = $this->xpath->query('*', $nodeElement);
                    if (
                        $nodeList
                        && $nodeList->length == 1
                        && $nodeList->item(0)->nodeName == 'mchar'
                        && $nodeList->item(0) instanceof DOMElement
                        && $nodeList->item(0)->hasAttribute('name')
                    ) {
                        $nodeValue = $nodeList->item(0)->getAttribute('name');
                    }
                }

                return new Element\Operator($nodeValue);
            case 'mrow':
                $mrow = new Element\Row();

                $this->parseNode($nodeElement, $mrow);

                return $mrow;
            case 'msup':
                $nodeList = $this->xpath->query('*', $nodeElement);
                if ($nodeList && $nodeList->length == 2) {
                    return new Element\Superscript(
                        $this->getElement($nodeList->item(0)),
                        $this->getElement($nodeList->item(1))
                    );
                }

                throw new InvalidInputException(sprintf(
                    '%s : The tag `%s` has not two subelements',
                    __METHOD__,
                    $nodeElement->nodeName
                ));
            case 'semantics':
                $semantics = new Element\Semantics();

                $this->parseNode($nodeElement, $semantics);

                return $semantics;
            default:
                throw new NotImplementedException(sprintf(
                    '%s : The tag `%s` is not implemented',
                    __METHOD__,
                    $nodeElement->nodeName
                ));
        }
    }
}
