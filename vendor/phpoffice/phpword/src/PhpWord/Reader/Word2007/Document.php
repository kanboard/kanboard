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

use DOMElement;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\XMLReader;

/**
 * Document reader.
 *
 * @since 0.10.0
 *
 * @SuppressWarnings(PHPMD.UnusedPrivateMethod) For readWPNode
 */
class Document extends AbstractPart
{
    /**
     * PhpWord object.
     *
     * @var PhpWord
     */
    private $phpWord;

    /**
     * Read document.xml.
     */
    public function read(PhpWord $phpWord): void
    {
        $this->phpWord = $phpWord;
        $xmlReader = new XMLReader();
        $xmlReader->getDomFromZip($this->docFile, $this->xmlFile);
        $readMethods = ['w:p' => 'readWPNode', 'w:tbl' => 'readTable', 'w:sectPr' => 'readWSectPrNode'];

        $nodes = $xmlReader->getElements('w:body/*');
        if ($nodes->length > 0) {
            $section = $this->phpWord->addSection();
            foreach ($nodes as $node) {
                if (isset($readMethods[$node->nodeName])) {
                    $readMethod = $readMethods[$node->nodeName];
                    $this->$readMethod($xmlReader, $node, $section);
                }
            }
        }
    }

    /**
     * Read header footer.
     *
     * @param array $settings
     */
    private function readHeaderFooter($settings, Section &$section): void
    {
        $readMethods = ['w:p' => 'readParagraph', 'w:tbl' => 'readTable'];

        if (is_array($settings) && isset($settings['hf'])) {
            foreach ($settings['hf'] as $rId => $hfSetting) {
                if (isset($this->rels['document'][$rId])) {
                    [$hfType, $xmlFile, $docPart] = array_values($this->rels['document'][$rId]);
                    $addMethod = "add{$hfType}";
                    $hfObject = $section->$addMethod($hfSetting['type']);

                    // Read header/footer content
                    $xmlReader = new XMLReader();
                    $xmlReader->getDomFromZip($this->docFile, $xmlFile);
                    $nodes = $xmlReader->getElements('*');
                    if ($nodes->length > 0) {
                        foreach ($nodes as $node) {
                            if (isset($readMethods[$node->nodeName])) {
                                $readMethod = $readMethods[$node->nodeName];
                                $this->$readMethod($xmlReader, $node, $hfObject, $docPart);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Read w:sectPr.
     *
     * @ignoreScrutinizerPatch
     *
     * @return array
     */
    private function readSectionStyle(XMLReader $xmlReader, DOMElement $domNode)
    {
        $styleDefs = [
            'breakType' => [self::READ_VALUE, 'w:type'],
            'vAlign' => [self::READ_VALUE, 'w:vAlign'],
            'pageSizeW' => [self::READ_VALUE, 'w:pgSz', 'w:w'],
            'pageSizeH' => [self::READ_VALUE, 'w:pgSz', 'w:h'],
            'orientation' => [self::READ_VALUE, 'w:pgSz', 'w:orient'],
            'colsNum' => [self::READ_VALUE, 'w:cols', 'w:num'],
            'colsSpace' => [self::READ_VALUE, 'w:cols', 'w:space'],
            'marginTop' => [self::READ_VALUE, 'w:pgMar', 'w:top'],
            'marginLeft' => [self::READ_VALUE, 'w:pgMar', 'w:left'],
            'marginBottom' => [self::READ_VALUE, 'w:pgMar', 'w:bottom'],
            'marginRight' => [self::READ_VALUE, 'w:pgMar', 'w:right'],
            'headerHeight' => [self::READ_VALUE, 'w:pgMar', 'w:header'],
            'footerHeight' => [self::READ_VALUE, 'w:pgMar', 'w:footer'],
            'gutter' => [self::READ_VALUE, 'w:pgMar', 'w:gutter'],
        ];
        $styles = $this->readStyleDefs($xmlReader, $domNode, $styleDefs);

        // Header and footer
        // @todo Cleanup this part
        $nodes = $xmlReader->getElements('*', $domNode);
        foreach ($nodes as $node) {
            if ($node->nodeName == 'w:headerReference' || $node->nodeName == 'w:footerReference') {
                $id = $xmlReader->getAttribute('r:id', $node);
                $styles['hf'][$id] = [
                    'method' => str_replace('w:', '', str_replace('Reference', '', $node->nodeName)),
                    'type' => $xmlReader->getAttribute('w:type', $node),
                ];
            }
        }

        return $styles;
    }

    /**
     * Read w:p node.
     */
    private function readWPNode(XMLReader $xmlReader, DOMElement $node, Section &$section): void
    {
        // Page break
        if ($xmlReader->getAttribute('w:type', $node, 'w:r/w:br') == 'page') {
            $section->addPageBreak(); // PageBreak
        }

        // Paragraph
        $this->readParagraph($xmlReader, $node, $section);

        // Section properties
        if ($xmlReader->elementExists('w:pPr/w:sectPr', $node)) {
            $sectPrNode = $xmlReader->getElement('w:pPr/w:sectPr', $node);
            if ($sectPrNode !== null) {
                $this->readWSectPrNode($xmlReader, $sectPrNode, $section);
            }
            $section = $this->phpWord->addSection();
        }
    }

    /**
     * Read w:sectPr node.
     */
    private function readWSectPrNode(XMLReader $xmlReader, DOMElement $node, Section &$section): void
    {
        $style = $this->readSectionStyle($xmlReader, $node);
        $section->setStyle($style);
        $this->readHeaderFooter($style, $section);
    }
}
