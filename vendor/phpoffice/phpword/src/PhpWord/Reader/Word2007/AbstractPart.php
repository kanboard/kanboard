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

namespace PhpOffice\PhpWord\Reader\Word2007;

use DateTime;
use DOMElement;
use InvalidArgumentException;
use PhpOffice\Math\Reader\OfficeMathML;
use PhpOffice\PhpWord\ComplexType\RubyProperties;
use PhpOffice\PhpWord\ComplexType\TblWidth as TblWidthComplexType;
use PhpOffice\PhpWord\Element\AbstractContainer;
use PhpOffice\PhpWord\Element\AbstractElement;
use PhpOffice\PhpWord\Element\FormField;
use PhpOffice\PhpWord\Element\Ruby;
use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Element\TrackChange;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\XMLReader;

/**
 * Abstract part reader.
 *
 * This class is inherited by ODText reader
 *
 * @since 0.10.0
 */
abstract class AbstractPart
{
    /**
     * Conversion method.
     *
     * @const int
     */
    const READ_VALUE = 'attributeValue';            // Read attribute value
    const READ_EQUAL = 'attributeEquals';           // Read `true` when attribute value equals specified value
    const READ_TRUE = 'attributeTrue';              // Read `true` when element exists
    const READ_FALSE = 'attributeFalse';            // Read `false` when element exists
    const READ_SIZE = 'attributeMultiplyByTwo';     // Read special attribute value for Font::$size

    /**
     * Document file.
     *
     * @var string
     */
    protected $docFile;

    /**
     * XML file.
     *
     * @var string
     */
    protected $xmlFile;

    /**
     * Part relationships.
     *
     * @var array
     */
    protected $rels = [];

    /**
     * Comment references.
     *
     * @var array<string, array<string, AbstractElement>>
     */
    protected $commentRefs = [];

    /**
     * Image Loading.
     *
     * @var bool
     */
    protected $imageLoading = true;

    /**
     * Read part.
     */
    abstract public function read(PhpWord $phpWord);

    /**
     * Create new instance.
     *
     * @param string $docFile
     * @param string $xmlFile
     */
    public function __construct($docFile, $xmlFile)
    {
        $this->docFile = $docFile;
        $this->xmlFile = $xmlFile;
    }

    /**
     * Set relationships.
     *
     * @param array $value
     */
    public function setRels($value): void
    {
        $this->rels = $value;
    }

    public function setImageLoading(bool $value): self
    {
        $this->imageLoading = $value;

        return $this;
    }

    public function hasImageLoading(): bool
    {
        return $this->imageLoading;
    }

    /**
     * Get comment references.
     *
     * @return array<string, array<string, null|AbstractElement>>
     */
    public function getCommentReferences(): array
    {
        return $this->commentRefs;
    }

    /**
     * Set comment references.
     *
     * @param array<string, array<string, null|AbstractElement>> $commentRefs
     */
    public function setCommentReferences(array $commentRefs): self
    {
        $this->commentRefs = $commentRefs;

        return $this;
    }

    /**
     * Set comment reference.
     */
    private function setCommentReference(string $type, string $id, AbstractElement $element): self
    {
        if (!in_array($type, ['start', 'end'])) {
            throw new InvalidArgumentException('Type must be "start" or "end"');
        }

        if (!array_key_exists($id, $this->commentRefs)) {
            $this->commentRefs[$id] = [
                'start' => null,
                'end' => null,
            ];
        }
        $this->commentRefs[$id][$type] = $element;

        return $this;
    }

    /**
     * Get comment reference.
     *
     * @return array<string, null|AbstractElement>
     */
    protected function getCommentReference(string $id): array
    {
        if (!array_key_exists($id, $this->commentRefs)) {
            throw new InvalidArgumentException(sprintf('Comment with id %s isn\'t referenced in document', $id));
        }

        return $this->commentRefs[$id];
    }

    /**
     * Read w:p.
     *
     * @param AbstractContainer $parent
     * @param string $docPart
     *
     * @todo Get font style for preserve text
     */
    protected function readParagraph(XMLReader $xmlReader, DOMElement $domNode, $parent, $docPart = 'document'): void
    {
        // Paragraph style
        $paragraphStyle = $xmlReader->elementExists('w:pPr', $domNode) ? $this->readParagraphStyle($xmlReader, $domNode) : null;

        if ($xmlReader->elementExists('w:r/w:fldChar/w:ffData', $domNode)) {
            // FormField
            $partOfFormField = false;
            $formNodes = [];
            $formType = null;
            $textRunContainers = $xmlReader->countElements('w:r|w:ins|w:del|w:hyperlink|w:smartTag', $domNode);
            if ($textRunContainers > 0) {
                $nodes = $xmlReader->getElements('*', $domNode);
                $paragraph = $parent->addTextRun($paragraphStyle);
                foreach ($nodes as $node) {
                    if ($xmlReader->elementExists('w:fldChar/w:ffData', $node)) {
                        $partOfFormField = true;
                        $formNodes[] = $node;
                        if ($xmlReader->elementExists('w:fldChar/w:ffData/w:ddList', $node)) {
                            $formType = 'dropdown';
                        } elseif ($xmlReader->elementExists('w:fldChar/w:ffData/w:textInput', $node)) {
                            $formType = 'textinput';
                        } elseif ($xmlReader->elementExists('w:fldChar/w:ffData/w:checkBox', $node)) {
                            $formType = 'checkbox';
                        }
                    } elseif ($partOfFormField &&
                        $xmlReader->elementExists('w:fldChar', $node) &&
                        'end' == $xmlReader->getAttribute('w:fldCharType', $node, 'w:fldChar')
                    ) {
                        $formNodes[] = $node;
                        $partOfFormField = false;
                        // Process the form fields
                        $this->readFormField($xmlReader, $formNodes, $paragraph, $paragraphStyle, $formType);
                    } elseif ($partOfFormField) {
                        $formNodes[] = $node;
                    } else {
                        // normal runs
                        $this->readRun($xmlReader, $node, $paragraph, $docPart, $paragraphStyle);
                    }
                }
            }
        } elseif ($xmlReader->elementExists('w:r/w:instrText', $domNode)) {
            // PreserveText
            $ignoreText = false;
            $textContent = '';
            $fontStyle = $this->readFontStyle($xmlReader, $domNode);
            $nodes = $xmlReader->getElements('w:r', $domNode);
            foreach ($nodes as $node) {
                if ($xmlReader->elementExists('w:lastRenderedPageBreak', $node)) {
                    $parent->addPageBreak();
                }
                $instrText = $xmlReader->getValue('w:instrText', $node);
                if (null !== $instrText) {
                    $textContent .= '{' . $instrText . '}';
                } else {
                    if ($xmlReader->elementExists('w:fldChar', $node)) {
                        $fldCharType = $xmlReader->getAttribute('w:fldCharType', $node, 'w:fldChar');
                        if ('begin' == $fldCharType) {
                            $ignoreText = true;
                        } elseif ('end' == $fldCharType) {
                            $ignoreText = false;
                        }
                    }
                    if (false === $ignoreText) {
                        $textContent .= $xmlReader->getValue('w:t', $node);
                    }
                }
            }
            $parent->addPreserveText(htmlspecialchars($textContent, ENT_QUOTES, 'UTF-8'), $fontStyle, $paragraphStyle);

            return;
        }

        // Formula
        $xmlReader->registerNamespace('m', 'http://schemas.openxmlformats.org/officeDocument/2006/math');
        if ($xmlReader->elementExists('m:oMath', $domNode)) {
            $mathElement = $xmlReader->getElement('m:oMath', $domNode);
            $mathXML = $mathElement->ownerDocument->saveXML($mathElement);
            if (is_string($mathXML)) {
                $reader = new OfficeMathML();
                $math = $reader->read($mathXML);

                $parent->addFormula($math);
            }

            return;
        }

        // List item
        if ($xmlReader->elementExists('w:pPr/w:numPr', $domNode)) {
            $numId = $xmlReader->getAttribute('w:val', $domNode, 'w:pPr/w:numPr/w:numId');
            $levelId = $xmlReader->getAttribute('w:val', $domNode, 'w:pPr/w:numPr/w:ilvl');
            $nodes = $xmlReader->getElements('*', $domNode);

            $listItemRun = $parent->addListItemRun($levelId, "PHPWordList{$numId}", $paragraphStyle);

            foreach ($nodes as $node) {
                $this->readRun($xmlReader, $node, $listItemRun, $docPart, $paragraphStyle);
            }

            return;
        }

        // Heading or Title
        $headingDepth = $xmlReader->elementExists('w:pPr', $domNode) ? $this->getHeadingDepth($paragraphStyle) : null;
        if ($headingDepth !== null) {
            $textContent = null;
            $nodes = $xmlReader->getElements('w:r|w:hyperlink', $domNode);
            $hasRubyElement = $xmlReader->elementExists('w:r/w:ruby', $domNode);
            if ($nodes->length === 1 && !$hasRubyElement) {
                $textContent = htmlspecialchars($xmlReader->getValue('w:t', $nodes->item(0)), ENT_QUOTES, 'UTF-8');
            } else {
                $textContent = new TextRun($paragraphStyle);
                foreach ($nodes as $node) {
                    $this->readRun($xmlReader, $node, $textContent, $docPart, $paragraphStyle);
                }
            }
            $parent->addTitle($textContent, $headingDepth);

            return;
        }

        // Text and TextRun
        $textRunContainers = $xmlReader->countElements('w:r|w:ins|w:del|w:hyperlink|w:smartTag|w:commentReference|w:commentRangeStart|w:commentRangeEnd', $domNode);
        if (0 === $textRunContainers) {
            $parent->addTextBreak(1, $paragraphStyle);
        } else {
            $nodes = $xmlReader->getElements('*', $domNode);
            $paragraph = $parent->addTextRun($paragraphStyle);
            foreach ($nodes as $node) {
                $this->readRun($xmlReader, $node, $paragraph, $docPart, $paragraphStyle);
            }
        }
    }

    /**
     * @param DOMElement[] $domNodes
     * @param AbstractContainer $parent
     * @param mixed $paragraphStyle
     * @param string $formType
     */
    private function readFormField(XMLReader $xmlReader, array $domNodes, $parent, $paragraphStyle, $formType): void
    {
        if (!in_array($formType, ['textinput', 'checkbox', 'dropdown'])) {
            return;
        }

        $formField = $parent->addFormField($formType, null, $paragraphStyle);
        $ffData = $xmlReader->getElement('w:fldChar/w:ffData', $domNodes[0]);

        foreach ($xmlReader->getElements('*', $ffData) as $node) {
            /** @var DOMElement $node */
            switch ($node->localName) {
                case 'name':
                    $formField->setName($node->getAttribute('w:val'));

                    break;
                case 'ddList':
                    $listEntries = [];
                    foreach ($xmlReader->getElements('*', $node) as $ddListNode) {
                        switch ($ddListNode->localName) {
                            case 'result':
                                $formField->setValue($xmlReader->getAttribute('w:val', $ddListNode));

                                break;
                            case 'default':
                                $formField->setDefault($xmlReader->getAttribute('w:val', $ddListNode));

                                break;
                            case 'listEntry':
                                $listEntries[] = $xmlReader->getAttribute('w:val', $ddListNode);

                                break;
                        }
                    }
                    $formField->setEntries($listEntries);
                    if (null !== $formField->getValue()) {
                        $formField->setText($listEntries[$formField->getValue()]);
                    }

                    break;
                case 'textInput':
                    foreach ($xmlReader->getElements('*', $node) as $ddListNode) {
                        switch ($ddListNode->localName) {
                            case 'default':
                                $formField->setDefault($xmlReader->getAttribute('w:val', $ddListNode));

                                break;
                            case 'format':
                            case 'maxLength':
                                break;
                        }
                    }

                    break;
                case 'checkBox':
                    foreach ($xmlReader->getElements('*', $node) as $ddListNode) {
                        switch ($ddListNode->localName) {
                            case 'default':
                                $formField->setDefault($xmlReader->getAttribute('w:val', $ddListNode));

                                break;
                            case 'checked':
                                $formField->setValue($xmlReader->getAttribute('w:val', $ddListNode));

                                break;
                            case 'size':
                            case 'sizeAuto':
                                break;
                        }
                    }

                    break;
            }
        }

        if ('textinput' == $formType) {
            $ignoreText = true;
            $textContent = '';
            foreach ($domNodes as $node) {
                if ($xmlReader->elementExists('w:fldChar', $node)) {
                    $fldCharType = $xmlReader->getAttribute('w:fldCharType', $node, 'w:fldChar');
                    if ('separate' == $fldCharType) {
                        $ignoreText = false;
                    } elseif ('end' == $fldCharType) {
                        $ignoreText = true;
                    }
                }

                if (false === $ignoreText) {
                    $textContent .= $xmlReader->getValue('w:t', $node);
                }
            }
            $formField->setValue(htmlspecialchars($textContent, ENT_QUOTES, 'UTF-8'));
            $formField->setText(htmlspecialchars($textContent, ENT_QUOTES, 'UTF-8'));
        }
    }

    /**
     * Returns the depth of the Heading, returns 0 for a Title.
     *
     * @return null|number
     */
    private function getHeadingDepth(?array $paragraphStyle = null)
    {
        if (is_array($paragraphStyle) && isset($paragraphStyle['styleName'])) {
            if ('Title' === $paragraphStyle['styleName']) {
                return 0;
            }

            $headingMatches = [];
            preg_match('/Heading(\d)/', $paragraphStyle['styleName'], $headingMatches);
            if (!empty($headingMatches)) {
                return $headingMatches[1];
            }
        }

        return null;
    }

    /**
     * Read w:r.
     *
     * @param AbstractContainer $parent
     * @param string $docPart
     * @param mixed $paragraphStyle
     *
     * @todo Footnote paragraph style
     */
    protected function readRun(XMLReader $xmlReader, DOMElement $domNode, $parent, $docPart, $paragraphStyle = null): void
    {
        if (in_array($domNode->nodeName, ['w:ins', 'w:del', 'w:smartTag', 'w:hyperlink', 'w:commentReference'])) {
            $nodes = $xmlReader->getElements('*', $domNode);
            foreach ($nodes as $node) {
                $this->readRun($xmlReader, $node, $parent, $docPart, $paragraphStyle);
            }
        } elseif ($domNode->nodeName == 'w:r') {
            $fontStyle = $this->readFontStyle($xmlReader, $domNode);
            $nodes = $xmlReader->getElements('*', $domNode);
            foreach ($nodes as $node) {
                $this->readRunChild($xmlReader, $node, $parent, $docPart, $paragraphStyle, $fontStyle);
            }
        }

        if ($xmlReader->elementExists('.//*["commentReference"=local-name()]', $domNode)) {
            $node = iterator_to_array($xmlReader->getElements('.//*["commentReference"=local-name()]', $domNode))[0];
            $attributeIdentifier = $node->attributes->getNamedItem('id');
            if ($attributeIdentifier) {
                $id = $attributeIdentifier->nodeValue;

                $this->setCommentReference('start', $id, $parent->getElement($parent->countElements() - 1));
                $this->setCommentReference('end', $id, $parent->getElement($parent->countElements() - 1));
            }
        }
    }

    /**
     * Parses nodes under w:r.
     *
     * @param string $docPart
     * @param mixed $paragraphStyle
     * @param mixed $fontStyle
     */
    protected function readRunChild(XMLReader $xmlReader, DOMElement $node, AbstractContainer $parent, $docPart, $paragraphStyle = null, $fontStyle = null): void
    {
        $runParent = $node->parentNode->parentNode;
        if ($node->nodeName == 'w:footnoteReference') {
            // Footnote
            $wId = $xmlReader->getAttribute('w:id', $node);
            $footnote = $parent->addFootnote();
            $footnote->setRelationId($wId);
        } elseif ($node->nodeName == 'w:endnoteReference') {
            // Endnote
            $wId = $xmlReader->getAttribute('w:id', $node);
            $endnote = $parent->addEndnote();
            $endnote->setRelationId($wId);
        } elseif ($node->nodeName == 'w:pict') {
            // Image
            $rId = $xmlReader->getAttribute('r:id', $node, 'v:shape/v:imagedata');
            $target = $this->getMediaTarget($docPart, $rId);
            if ($this->hasImageLoading() && null !== $target) {
                if ('External' == $this->getTargetMode($docPart, $rId)) {
                    $imageSource = $target;
                } else {
                    $imageSource = "zip://{$this->docFile}#{$target}";
                }
                $parent->addImage($imageSource);
            }
        } elseif ($node->nodeName == 'w:drawing') {
            // Office 2011 Image
            $xmlReader->registerNamespace('wp', 'http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing');
            $xmlReader->registerNamespace('r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');
            $xmlReader->registerNamespace('pic', 'http://schemas.openxmlformats.org/drawingml/2006/picture');
            $xmlReader->registerNamespace('a', 'http://schemas.openxmlformats.org/drawingml/2006/main');

            $name = $xmlReader->getAttribute('name', $node, 'wp:inline/a:graphic/a:graphicData/pic:pic/pic:nvPicPr/pic:cNvPr');
            $embedId = $xmlReader->getAttribute('r:embed', $node, 'wp:inline/a:graphic/a:graphicData/pic:pic/pic:blipFill/a:blip');
            if ($name === null && $embedId === null) { // some Converters puts images on a different path
                $name = $xmlReader->getAttribute('name', $node, 'wp:anchor/a:graphic/a:graphicData/pic:pic/pic:nvPicPr/pic:cNvPr');
                $embedId = $xmlReader->getAttribute('r:embed', $node, 'wp:anchor/a:graphic/a:graphicData/pic:pic/pic:blipFill/a:blip');
            }
            $target = $this->getMediaTarget($docPart, $embedId);
            if ($this->hasImageLoading() && null !== $target) {
                $imageSource = "zip://{$this->docFile}#{$target}";
                $parent->addImage($imageSource, null, false, $name);
            }
        } elseif ($node->nodeName == 'w:object') {
            // Object
            $rId = $xmlReader->getAttribute('r:id', $node, 'o:OLEObject');
            // $rIdIcon = $xmlReader->getAttribute('r:id', $domNode, 'w:object/v:shape/v:imagedata');
            $target = $this->getMediaTarget($docPart, $rId);
            if (null !== $target) {
                $textContent = "&lt;Object: {$target}>";
                $parent->addText($textContent, $fontStyle, $paragraphStyle);
            }
        } elseif ($node->nodeName == 'w:br') {
            $parent->addTextBreak();
        } elseif ($node->nodeName == 'w:tab') {
            $parent->addText("\t");
        } elseif ($node->nodeName == 'mc:AlternateContent') {
            if ($node->hasChildNodes()) {
                // Get fallback instead of mc:Choice to make sure it is compatible
                $fallbackElements = $node->getElementsByTagName('Fallback');

                if ($fallbackElements->length) {
                    $fallback = $fallbackElements->item(0);
                    // TextRun
                    $textContent = htmlspecialchars($fallback->nodeValue, ENT_QUOTES, 'UTF-8');

                    $parent->addText($textContent, $fontStyle, $paragraphStyle);
                }
            }
        } elseif ($node->nodeName == 'w:t' || $node->nodeName == 'w:delText') {
            // TextRun
            $textContent = htmlspecialchars($xmlReader->getValue('.', $node), ENT_QUOTES, 'UTF-8');

            if ($runParent->nodeName == 'w:hyperlink') {
                $rId = $xmlReader->getAttribute('r:id', $runParent);
                $target = $this->getMediaTarget($docPart, $rId);
                if (null !== $target) {
                    $parent->addLink($target, $textContent, $fontStyle, $paragraphStyle);
                } else {
                    $parent->addText($textContent, $fontStyle, $paragraphStyle);
                }
            } else {
                /** @var AbstractElement $element */
                $element = $parent->addText($textContent, $fontStyle, $paragraphStyle);
                if (in_array($runParent->nodeName, ['w:ins', 'w:del'])) {
                    $type = ($runParent->nodeName == 'w:del') ? TrackChange::DELETED : TrackChange::INSERTED;
                    $author = $runParent->getAttribute('w:author');
                    $date = DateTime::createFromFormat('Y-m-d\TH:i:s\Z', $runParent->getAttribute('w:date'));
                    $date = $date instanceof DateTime ? $date : null;
                    $element->setChangeInfo($type, $author, $date);
                }
            }
        } elseif ($node->nodeName == 'w:softHyphen') {
            $element = $parent->addText("\u{200c}", $fontStyle, $paragraphStyle);
        } elseif ($node->nodeName == 'w:ruby') {
            $rubyPropertiesNode = $xmlReader->getElement('w:rubyPr', $node);
            $properties = $this->readRubyProperties($xmlReader, $rubyPropertiesNode);
            // read base text node
            $baseText = new TextRun($paragraphStyle);
            $baseTextNode = $xmlReader->getElement('w:rubyBase/w:r', $node);
            $this->readRun($xmlReader, $baseTextNode, $baseText, $docPart, $paragraphStyle);
            // read the actual ruby text (e.g. furigana in Japanese)
            $rubyText = new TextRun($paragraphStyle);
            $rubyTextNode = $xmlReader->getElement('w:rt/w:r', $node);
            $this->readRun($xmlReader, $rubyTextNode, $rubyText, $docPart, $paragraphStyle);
            // add element to parent
            $parent->addRuby($baseText, $rubyText, $properties);
        }
    }

    /**
     * Read w:rubyPr element.
     *
     * @param XMLReader $xmlReader reader for XML
     * @param DOMElement $domNode w:RubyPr element
     *
     * @return RubyProperties ruby properties from element
     */
    protected function readRubyProperties(XMLReader $xmlReader, DOMElement $domNode): RubyProperties
    {
        $rubyAlignment = $xmlReader->getElement('w:rubyAlign', $domNode)->getAttribute('w:val');
        $rubyHps = $xmlReader->getElement('w:hps', $domNode)->getAttribute('w:val'); // font face
        $rubyHpsRaise = $xmlReader->getElement('w:hpsRaise', $domNode)->getAttribute('w:val'); // pts above base text
        $rubyHpsBaseText = $xmlReader->getElement('w:hpsBaseText', $domNode)->getAttribute('w:val'); // base text size
        $rubyLid = $xmlReader->getElement('w:lid', $domNode)->getAttribute('w:val'); // type of ruby
        $properties = new RubyProperties();
        $properties->setAlignment($rubyAlignment);
        $properties->setFontFaceSize((float) $rubyHps);
        $properties->setFontPointsAboveBaseText((float) $rubyHpsRaise);
        $properties->setFontSizeForBaseText((float) $rubyHpsBaseText);
        $properties->setLanguageId($rubyLid);

        return $properties;
    }

    /**
     * Read w:tbl.
     *
     * @param mixed $parent
     * @param string $docPart
     */
    protected function readTable(XMLReader $xmlReader, DOMElement $domNode, $parent, $docPart = 'document'): void
    {
        // Table style
        $tblStyle = null;
        if ($xmlReader->elementExists('w:tblPr', $domNode)) {
            $tblStyle = $this->readTableStyle($xmlReader, $domNode);
        }

        /** @var \PhpOffice\PhpWord\Element\Table $table Type hint */
        $table = $parent->addTable($tblStyle);
        $tblNodes = $xmlReader->getElements('*', $domNode);
        foreach ($tblNodes as $tblNode) {
            if ('w:tblGrid' == $tblNode->nodeName) { // Column
                // @todo Do something with table columns
            } elseif ('w:tr' == $tblNode->nodeName) { // Row
                $rowHeight = $xmlReader->getAttribute('w:val', $tblNode, 'w:trPr/w:trHeight');
                $rowHRule = $xmlReader->getAttribute('w:hRule', $tblNode, 'w:trPr/w:trHeight');
                $rowHRule = $rowHRule == 'exact';
                $rowStyle = [
                    'tblHeader' => $xmlReader->elementExists('w:trPr/w:tblHeader', $tblNode),
                    'cantSplit' => $xmlReader->elementExists('w:trPr/w:cantSplit', $tblNode),
                    'exactHeight' => $rowHRule,
                ];

                $row = $table->addRow($rowHeight, $rowStyle);
                $rowNodes = $xmlReader->getElements('*', $tblNode);
                foreach ($rowNodes as $rowNode) {
                    if ('w:trPr' == $rowNode->nodeName) { // Row style
                        // @todo Do something with row style
                    } elseif ('w:tc' == $rowNode->nodeName) { // Cell
                        $cellWidth = $xmlReader->getAttribute('w:w', $rowNode, 'w:tcPr/w:tcW');
                        $cellStyle = null;
                        if ($xmlReader->elementExists('w:tcPr', $rowNode)) {
                            $cellStyle = $this->readCellStyle($xmlReader, $rowNode);
                        }

                        $cell = $row->addCell($cellWidth, $cellStyle);
                        $cellNodes = $xmlReader->getElements('*', $rowNode);
                        foreach ($cellNodes as $cellNode) {
                            if ('w:p' == $cellNode->nodeName) { // Paragraph
                                $this->readParagraph($xmlReader, $cellNode, $cell, $docPart);
                            } elseif ($cellNode->nodeName == 'w:tbl') { // Table
                                $this->readTable($xmlReader, $cellNode, $cell, $docPart);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Read w:pPr.
     *
     * @return null|array
     */
    protected function readParagraphStyle(XMLReader $xmlReader, DOMElement $domNode)
    {
        if (!$xmlReader->elementExists('w:pPr', $domNode)) {
            return null;
        }

        $styleNode = $xmlReader->getElement('w:pPr', $domNode);
        $styleDefs = [
            'styleName' => [self::READ_VALUE, ['w:pStyle', 'w:name']],
            'alignment' => [self::READ_VALUE, 'w:jc'],
            'basedOn' => [self::READ_VALUE, 'w:basedOn'],
            'next' => [self::READ_VALUE, 'w:next'],
            'indentLeft' => [self::READ_VALUE, 'w:ind', 'w:left'],
            'indentRight' => [self::READ_VALUE, 'w:ind', 'w:right'],
            'indentHanging' => [self::READ_VALUE, 'w:ind', 'w:hanging'],
            'indentFirstLine' => [self::READ_VALUE, 'w:ind', 'w:firstLine'],
            'indentFirstLineChars' => [self::READ_VALUE, 'w:ind', 'w:firstLineChars'],
            'spaceAfter' => [self::READ_VALUE, 'w:spacing', 'w:after'],
            'spaceBefore' => [self::READ_VALUE, 'w:spacing', 'w:before'],
            'widowControl' => [self::READ_FALSE, 'w:widowControl'],
            'keepNext' => [self::READ_TRUE,  'w:keepNext'],
            'keepLines' => [self::READ_TRUE,  'w:keepLines'],
            'pageBreakBefore' => [self::READ_TRUE,  'w:pageBreakBefore'],
            'contextualSpacing' => [self::READ_TRUE,  'w:contextualSpacing'],
            'bidi' => [self::READ_TRUE,  'w:bidi'],
            'suppressAutoHyphens' => [self::READ_TRUE,  'w:suppressAutoHyphens'],
            'borderTopStyle' => [self::READ_VALUE, 'w:pBdr/w:top'],
            'borderTopColor' => [self::READ_VALUE, 'w:pBdr/w:top', 'w:color'],
            'borderTopSize' => [self::READ_VALUE, 'w:pBdr/w:top', 'w:sz'],
            'borderRightStyle' => [self::READ_VALUE, 'w:pBdr/w:right'],
            'borderRightColor' => [self::READ_VALUE, 'w:pBdr/w:right', 'w:color'],
            'borderRightSize' => [self::READ_VALUE, 'w:pBdr/w:right', 'w:sz'],
            'borderBottomStyle' => [self::READ_VALUE, 'w:pBdr/w:bottom'],
            'borderBottomColor' => [self::READ_VALUE, 'w:pBdr/w:bottom', 'w:color'],
            'borderBottomSize' => [self::READ_VALUE, 'w:pBdr/w:bottom', 'w:sz'],
            'borderLeftStyle' => [self::READ_VALUE, 'w:pBdr/w:left'],
            'borderLeftColor' => [self::READ_VALUE, 'w:pBdr/w:left', 'w:color'],
            'borderLeftSize' => [self::READ_VALUE, 'w:pBdr/w:left', 'w:sz'],
        ];

        return $this->readStyleDefs($xmlReader, $styleNode, $styleDefs);
    }

    /**
     * Read w:rPr.
     *
     * @return null|array
     */
    protected function readFontStyle(XMLReader $xmlReader, DOMElement $domNode)
    {
        if (null === $domNode) {
            return null;
        }
        // Hyperlink has an extra w:r child
        if ('w:hyperlink' == $domNode->nodeName) {
            $domNode = $xmlReader->getElement('w:r', $domNode);
        }
        if (!$xmlReader->elementExists('w:rPr', $domNode)) {
            return null;
        }

        $styleNode = $xmlReader->getElement('w:rPr', $domNode);
        $styleDefs = [
            'styleName' => [self::READ_VALUE, 'w:rStyle'],
            'name' => [self::READ_VALUE, 'w:rFonts', ['w:ascii', 'w:hAnsi', 'w:eastAsia', 'w:cs']],
            'hint' => [self::READ_VALUE, 'w:rFonts', 'w:hint'],
            'size' => [self::READ_SIZE,  ['w:sz', 'w:szCs']],
            'color' => [self::READ_VALUE, 'w:color'],
            'underline' => [self::READ_VALUE, 'w:u'],
            'bold' => [self::READ_TRUE,  'w:b'],
            'italic' => [self::READ_TRUE,  'w:i'],
            'strikethrough' => [self::READ_TRUE,  'w:strike'],
            'doubleStrikethrough' => [self::READ_TRUE,  'w:dstrike'],
            'smallCaps' => [self::READ_TRUE,  'w:smallCaps'],
            'allCaps' => [self::READ_TRUE,  'w:caps'],
            'superScript' => [self::READ_EQUAL, 'w:vertAlign', 'w:val', 'superscript'],
            'subScript' => [self::READ_EQUAL, 'w:vertAlign', 'w:val', 'subscript'],
            'fgColor' => [self::READ_VALUE, 'w:highlight'],
            'rtl' => [self::READ_TRUE,  'w:rtl'],
            'lang' => [self::READ_VALUE, 'w:lang'],
            'position' => [self::READ_VALUE, 'w:position'],
            'hidden' => [self::READ_TRUE,  'w:vanish'],
        ];

        return $this->readStyleDefs($xmlReader, $styleNode, $styleDefs);
    }

    /**
     * Read w:tblPr.
     *
     * @return null|array|string
     *
     * @todo Capture w:tblStylePr w:type="firstRow"
     */
    protected function readTableStyle(XMLReader $xmlReader, DOMElement $domNode)
    {
        $style = null;
        $margins = ['top', 'left', 'bottom', 'right'];
        $borders = array_merge($margins, ['insideH', 'insideV']);

        if ($xmlReader->elementExists('w:tblPr', $domNode)) {
            if ($xmlReader->elementExists('w:tblPr/w:tblStyle', $domNode)) {
                $style = $xmlReader->getAttribute('w:val', $domNode, 'w:tblPr/w:tblStyle');
            } else {
                $styleNode = $xmlReader->getElement('w:tblPr', $domNode);
                $styleDefs = [];
                foreach ($margins as $side) {
                    $ucfSide = ucfirst($side);
                    $styleDefs["cellMargin$ucfSide"] = [self::READ_VALUE, "w:tblCellMar/w:$side", 'w:w'];
                }
                foreach ($borders as $side) {
                    $ucfSide = ucfirst($side);
                    $styleDefs["border{$ucfSide}Size"] = [self::READ_VALUE, "w:tblBorders/w:$side", 'w:sz'];
                    $styleDefs["border{$ucfSide}Color"] = [self::READ_VALUE, "w:tblBorders/w:$side", 'w:color'];
                    $styleDefs["border{$ucfSide}Style"] = [self::READ_VALUE, "w:tblBorders/w:$side", 'w:val'];
                }
                $styleDefs['layout'] = [self::READ_VALUE, 'w:tblLayout', 'w:type'];
                $styleDefs['bidiVisual'] = [self::READ_TRUE, 'w:bidiVisual'];
                $styleDefs['cellSpacing'] = [self::READ_VALUE, 'w:tblCellSpacing', 'w:w'];
                $style = $this->readStyleDefs($xmlReader, $styleNode, $styleDefs);

                $tablePositionNode = $xmlReader->getElement('w:tblpPr', $styleNode);
                if ($tablePositionNode !== null) {
                    $style['position'] = $this->readTablePosition($xmlReader, $tablePositionNode);
                }

                $indentNode = $xmlReader->getElement('w:tblInd', $styleNode);
                if ($indentNode !== null) {
                    $style['indent'] = $this->readTableIndent($xmlReader, $indentNode);
                }
            }
        }

        return $style;
    }

    /**
     * Read w:tblpPr.
     *
     * @return array
     */
    private function readTablePosition(XMLReader $xmlReader, DOMElement $domNode)
    {
        $styleDefs = [
            'leftFromText' => [self::READ_VALUE, '.', 'w:leftFromText'],
            'rightFromText' => [self::READ_VALUE, '.', 'w:rightFromText'],
            'topFromText' => [self::READ_VALUE, '.', 'w:topFromText'],
            'bottomFromText' => [self::READ_VALUE, '.', 'w:bottomFromText'],
            'vertAnchor' => [self::READ_VALUE, '.', 'w:vertAnchor'],
            'horzAnchor' => [self::READ_VALUE, '.', 'w:horzAnchor'],
            'tblpXSpec' => [self::READ_VALUE, '.', 'w:tblpXSpec'],
            'tblpX' => [self::READ_VALUE, '.', 'w:tblpX'],
            'tblpYSpec' => [self::READ_VALUE, '.', 'w:tblpYSpec'],
            'tblpY' => [self::READ_VALUE, '.', 'w:tblpY'],
        ];

        return $this->readStyleDefs($xmlReader, $domNode, $styleDefs);
    }

    /**
     * Read w:tblInd.
     *
     * @return TblWidthComplexType
     */
    private function readTableIndent(XMLReader $xmlReader, DOMElement $domNode)
    {
        $styleDefs = [
            'value' => [self::READ_VALUE, '.', 'w:w'],
            'type' => [self::READ_VALUE, '.', 'w:type'],
        ];
        $styleDefs = $this->readStyleDefs($xmlReader, $domNode, $styleDefs);

        return new TblWidthComplexType((int) $styleDefs['value'], $styleDefs['type']);
    }

    /**
     * Read w:tcPr.
     *
     * @return null|array
     */
    private function readCellStyle(XMLReader $xmlReader, DOMElement $domNode)
    {
        $styleDefs = [
            'valign' => [self::READ_VALUE, 'w:vAlign'],
            'textDirection' => [self::READ_VALUE, 'w:textDirection'],
            'gridSpan' => [self::READ_VALUE, 'w:gridSpan'],
            'vMerge' => [self::READ_VALUE, 'w:vMerge', null, null, 'continue'],
            'bgColor' => [self::READ_VALUE, 'w:shd', 'w:fill'],
            'noWrap' => [self::READ_VALUE, 'w:noWrap', null, null, true],
        ];
        $style = null;

        if ($xmlReader->elementExists('w:tcPr', $domNode)) {
            $styleNode = $xmlReader->getElement('w:tcPr', $domNode);

            $borders = ['top', 'left', 'bottom', 'right'];
            foreach ($borders as $side) {
                $ucfSide = ucfirst($side);

                $styleDefs['border' . $ucfSide . 'Size'] = [self::READ_VALUE, 'w:tcBorders/w:' . $side, 'w:sz'];
                $styleDefs['border' . $ucfSide . 'Color'] = [self::READ_VALUE, 'w:tcBorders/w:' . $side, 'w:color'];
                $styleDefs['border' . $ucfSide . 'Style'] = [self::READ_VALUE, 'w:tcBorders/w:' . $side, 'w:val'];
            }

            $style = $this->readStyleDefs($xmlReader, $styleNode, $styleDefs);
        }

        return $style;
    }

    /**
     * Returns the first child element found.
     *
     * @param null|array|string $elements
     *
     * @return null|string
     */
    private function findPossibleElement(XMLReader $xmlReader, ?DOMElement $parentNode = null, $elements = null)
    {
        if (is_array($elements)) {
            //if element is an array, we take the first element that exists in the XML
            foreach ($elements as $possibleElement) {
                if ($xmlReader->elementExists($possibleElement, $parentNode)) {
                    return $possibleElement;
                }
            }
        } else {
            return $elements;
        }

        return null;
    }

    /**
     * Returns the first attribute found.
     *
     * @param array|string $attributes
     *
     * @return null|string
     */
    private function findPossibleAttribute(XMLReader $xmlReader, DOMElement $node, $attributes)
    {
        //if attribute is an array, we take the first attribute that exists in the XML
        if (is_array($attributes)) {
            foreach ($attributes as $possibleAttribute) {
                if ($xmlReader->getAttribute($possibleAttribute, $node)) {
                    return $possibleAttribute;
                }
            }

            return null;
        }

        return $attributes;
    }

    /**
     * Read style definition.
     *
     * @param array $styleDefs
     *
     * @ignoreScrutinizerPatch
     *
     * @return array
     */
    protected function readStyleDefs(XMLReader $xmlReader, ?DOMElement $parentNode = null, $styleDefs = [])
    {
        $styles = [];

        foreach ($styleDefs as $styleProp => $styleVal) {
            [$method, $element, $attribute, $expected, $default] = array_pad($styleVal, 5, null);

            $element = $this->findPossibleElement($xmlReader, $parentNode, $element);
            if ($element === null) {
                continue;
            }

            if ($xmlReader->elementExists($element, $parentNode)) {
                $node = $xmlReader->getElement($element, $parentNode);

                $attribute = $this->findPossibleAttribute($xmlReader, $node, $attribute);

                // Use w:val as default if no attribute assigned
                $attribute = ($attribute === null) ? 'w:val' : $attribute;
                $attributeValue = $xmlReader->getAttribute($attribute, $node) ?? $default;

                $styleValue = $this->readStyleDef($method, $attributeValue, $expected);
                if ($styleValue !== null) {
                    $styles[$styleProp] = $styleValue;
                }
            }
        }

        return $styles;
    }

    /**
     * Return style definition based on conversion method.
     *
     * @param string $method
     *
     * @ignoreScrutinizerPatch
     *
     * @param null|string $attributeValue
     * @param mixed $expected
     *
     * @return mixed
     */
    private function readStyleDef($method, $attributeValue, $expected)
    {
        $style = $attributeValue;

        if (self::READ_SIZE == $method) {
            $style = $attributeValue / 2;
        } elseif (self::READ_TRUE == $method) {
            $style = $this->isOn($attributeValue);
        } elseif (self::READ_FALSE == $method) {
            $style = !$this->isOn($attributeValue);
        } elseif (self::READ_EQUAL == $method) {
            $style = $attributeValue == $expected;
        }

        return $style;
    }

    /**
     * Parses the value of the on/off value, null is considered true as it means the w:val attribute was not present.
     *
     * @see http://www.datypic.com/sc/ooxml/t-w_ST_OnOff.html
     *
     * @param string $value
     *
     * @return bool
     */
    private function isOn($value = null)
    {
        return $value === null || $value === '1' || $value === 'true' || $value === 'on';
    }

    /**
     * Returns the target of image, object, or link as stored in ::readMainRels.
     *
     * @param string $docPart
     * @param string $rId
     *
     * @return null|string
     */
    private function getMediaTarget($docPart, $rId)
    {
        $target = null;

        if (isset($this->rels[$docPart], $this->rels[$docPart][$rId])) {
            $target = $this->rels[$docPart][$rId]['target'];
        }

        return $target;
    }

    /**
     * Returns the target mode.
     *
     * @param string $docPart
     * @param string $rId
     *
     * @return null|string
     */
    private function getTargetMode($docPart, $rId)
    {
        $mode = null;

        if (isset($this->rels[$docPart], $this->rels[$docPart][$rId])) {
            $mode = $this->rels[$docPart][$rId]['targetMode'];
        }

        return $mode;
    }
}
