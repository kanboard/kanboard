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

namespace PhpOffice\PhpWord\Reader\ODText;

use DateTime;
use DOMElement;
use DOMNodeList;
use PhpOffice\Math\Reader\MathML;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\Element\TrackChange;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\XMLReader;

/**
 * Content reader.
 *
 * @since 0.10.0
 */
class Content extends AbstractPart
{
    /** @var ?Section */
    private $section;

    /**
     * Read content.xml.
     */
    public function read(PhpWord $phpWord): void
    {
        $xmlReader = new XMLReader();
        $xmlReader->getDomFromZip($this->docFile, $this->xmlFile);

        $trackedChanges = [];

        $nodes = $xmlReader->getElements('office:body/office:text/*');
        $this->section = null;
        $this->processNodes($nodes, $xmlReader, $phpWord);
        $this->section = null;
    }

    /** @param DOMNodeList<DOMElement> $nodes */
    public function processNodes(DOMNodeList $nodes, XMLReader $xmlReader, PhpWord $phpWord): void
    {
        if ($nodes->length > 0) {
            foreach ($nodes as $node) {
                // $styleName = $xmlReader->getAttribute('text:style-name', $node);
                switch ($node->nodeName) {
                    case 'text:h': // Heading
                        $depth = $xmlReader->getAttribute('text:outline-level', $node);
                        $this->getSection($phpWord)->addTitle($node->nodeValue, $depth);

                        break;
                    case 'text:p': // Paragraph
                        $styleName = $xmlReader->getAttribute('text:style-name', $node);
                        if (substr($styleName, 0, 2) === 'SB') {
                            break;
                        }
                        $element = $xmlReader->getElement('draw:frame/draw:object', $node);
                        if ($element) {
                            $mathFile = str_replace('./', '', $element->getAttribute('xlink:href')) . '/content.xml';

                            $xmlReaderObject = new XMLReader();
                            $mathElement = $xmlReaderObject->getDomFromZip($this->docFile, $mathFile);
                            if ($mathElement) {
                                $mathXML = $mathElement->saveXML($mathElement);

                                if (is_string($mathXML)) {
                                    $reader = new MathML();
                                    $math = $reader->read($mathXML);

                                    $this->getSection($phpWord)->addFormula($math);
                                }
                            }
                        } else {
                            $children = $node->childNodes;
                            $spans = false;
                            /** @var DOMElement $child */
                            foreach ($children as $child) {
                                switch ($child->nodeName) {
                                    case 'text:change-start':
                                        $changeId = $child->getAttribute('text:change-id');
                                        if (isset($trackedChanges[$changeId])) {
                                            $changed = $trackedChanges[$changeId];
                                        }

                                        break;
                                    case 'text:change-end':
                                        unset($changed);

                                        break;
                                    case 'text:change':
                                        $changeId = $child->getAttribute('text:change-id');
                                        if (isset($trackedChanges[$changeId])) {
                                            $changed = $trackedChanges[$changeId];
                                        }

                                        break;
                                    case 'text:span':
                                        $spans = true;

                                        break;
                                }
                            }

                            if ($spans) {
                                $element = $this->getSection($phpWord)->addTextRun();
                                foreach ($children as $child) {
                                    switch ($child->nodeName) {
                                        case 'text:span':
                                            /** @var DOMElement $child2 */
                                            foreach ($child->childNodes as $child2) {
                                                switch ($child2->nodeName) {
                                                    case '#text':
                                                        $element->addText($child2->nodeValue);

                                                        break;
                                                    case 'text:tab':
                                                        $element->addText("\t");

                                                        break;
                                                    case 'text:s':
                                                        $spaces = (int) $child2->getAttribute('text:c') ?: 1;
                                                        $element->addText(str_repeat(' ', $spaces));

                                                        break;
                                                }
                                            }

                                            break;
                                    }
                                }
                            } else {
                                $element = $this->getSection($phpWord)->addText($node->nodeValue);
                            }
                            if (isset($changed) && is_array($changed)) {
                                $element->setTrackChange($changed['changed']);
                                if (isset($changed['textNodes'])) {
                                    foreach ($changed['textNodes'] as $changedNode) {
                                        $element = $this->getSection($phpWord)->addText($changedNode->nodeValue);
                                        $element->setTrackChange($changed['changed']);
                                    }
                                }
                            }
                        }

                        break;
                    case 'text:list': // List
                        $listItems = $xmlReader->getElements('text:list-item/text:p', $node);
                        foreach ($listItems as $listItem) {
                            // $listStyleName = $xmlReader->getAttribute('text:style-name', $listItem);
                            $this->getSection($phpWord)->addListItem($listItem->nodeValue, 0);
                        }

                        break;
                    case 'text:tracked-changes':
                        $changedRegions = $xmlReader->getElements('text:changed-region', $node);
                        foreach ($changedRegions as $changedRegion) {
                            $type = ($changedRegion->firstChild->nodeName == 'text:insertion') ? TrackChange::INSERTED : TrackChange::DELETED;
                            $creatorNode = $xmlReader->getElements('office:change-info/dc:creator', $changedRegion->firstChild);
                            $author = $creatorNode[0]->nodeValue;
                            $dateNode = $xmlReader->getElements('office:change-info/dc:date', $changedRegion->firstChild);
                            $date = $dateNode[0]->nodeValue;
                            $date = preg_replace('/\.\d+$/', '', $date);
                            $date = DateTime::createFromFormat('Y-m-d\TH:i:s', $date);
                            $changed = new TrackChange($type, $author, $date);
                            $textNodes = $xmlReader->getElements('text:deletion/text:p', $changedRegion);
                            $trackedChanges[$changedRegion->getAttribute('text:id')] = ['changed' => $changed, 'textNodes' => $textNodes];
                        }

                        break;
                    case 'text:section': // Section
                        // $sectionStyleName = $xmlReader->getAttribute('text:style-name', $listItem);
                        $this->section = $phpWord->addSection();
                        $children = $node->childNodes;
                        $this->processNodes($children, $xmlReader, $phpWord);

                        break;
                }
            }
        }
    }

    private function getSection(PhpWord $phpWord): Section
    {
        $section = $this->section;
        if ($section === null) {
            $section = $this->section = $phpWord->addSection();
        }

        return $section;
    }
}
