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
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\XMLReader;

/**
 * Numbering reader.
 *
 * @since 0.10.0
 */
class Numbering extends AbstractPart
{
    /**
     * Read numbering.xml.
     */
    public function read(PhpWord $phpWord): void
    {
        $abstracts = [];
        $numberings = [];
        $xmlReader = new XMLReader();
        $xmlReader->getDomFromZip($this->docFile, $this->xmlFile);

        // Abstract numbering definition
        $nodes = $xmlReader->getElements('w:abstractNum');
        if ($nodes->length > 0) {
            foreach ($nodes as $node) {
                $abstractId = $xmlReader->getAttribute('w:abstractNumId', $node);
                $abstracts[$abstractId] = ['levels' => []];
                $abstract = &$abstracts[$abstractId];
                $subnodes = $xmlReader->getElements('*', $node);
                foreach ($subnodes as $subnode) {
                    switch ($subnode->nodeName) {
                        case 'w:multiLevelType':
                            $abstract['type'] = $xmlReader->getAttribute('w:val', $subnode);

                            break;
                        case 'w:lvl':
                            $levelId = $xmlReader->getAttribute('w:ilvl', $subnode);
                            $abstract['levels'][$levelId] = $this->readLevel($xmlReader, $subnode, $levelId);

                            break;
                    }
                }
            }
        }

        // Numbering instance definition
        $nodes = $xmlReader->getElements('w:num');
        if ($nodes->length > 0) {
            foreach ($nodes as $node) {
                $numId = $xmlReader->getAttribute('w:numId', $node);
                $abstractId = $xmlReader->getAttribute('w:val', $node, 'w:abstractNumId');
                $numberings[$numId] = $abstracts[$abstractId];
                $numberings[$numId]['numId'] = $numId;
                $subnodes = $xmlReader->getElements('w:lvlOverride/w:lvl', $node);
                foreach ($subnodes as $subnode) {
                    $levelId = $xmlReader->getAttribute('w:ilvl', $subnode);
                    $overrides = $this->readLevel($xmlReader, $subnode, $levelId);
                    foreach ($overrides as $key => $value) {
                        $numberings[$numId]['levels'][$levelId][$key] = $value;
                    }
                }
            }
        }

        // Push to Style collection
        foreach ($numberings as $numId => $numbering) {
            $phpWord->addNumberingStyle("PHPWordList{$numId}", $numbering);
        }
    }

    /**
     * Read numbering level definition from w:abstractNum and w:num.
     *
     * @param int $levelId
     *
     * @return array
     */
    private function readLevel(XMLReader $xmlReader, DOMElement $subnode, $levelId)
    {
        $level = [];

        $level['level'] = $levelId;
        $level['start'] = $xmlReader->getAttribute('w:val', $subnode, 'w:start');
        $level['format'] = $xmlReader->getAttribute('w:val', $subnode, 'w:numFmt');
        $level['restart'] = $xmlReader->getAttribute('w:val', $subnode, 'w:lvlRestart');
        $level['suffix'] = $xmlReader->getAttribute('w:val', $subnode, 'w:suff');
        $level['text'] = $xmlReader->getAttribute('w:val', $subnode, 'w:lvlText');
        $level['alignment'] = $xmlReader->getAttribute('w:val', $subnode, 'w:lvlJc');
        $level['tab'] = $xmlReader->getAttribute('w:pos', $subnode, 'w:pPr/w:tabs/w:tab');
        $level['left'] = $xmlReader->getAttribute('w:left', $subnode, 'w:pPr/w:ind');
        $level['hanging'] = $xmlReader->getAttribute('w:hanging', $subnode, 'w:pPr/w:ind');
        $level['font'] = $xmlReader->getAttribute('w:ascii', $subnode, 'w:rPr/w:rFonts');
        $level['hint'] = $xmlReader->getAttribute('w:hint', $subnode, 'w:rPr/w:rFonts');

        foreach ($level as $key => $value) {
            if (null === $value) {
                unset($level[$key]);
            }
        }

        return $level;
    }
}
