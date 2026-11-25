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

namespace PhpOffice\PhpWord\Reader;

use Exception;
use PhpOffice\PhpWord\Element\AbstractElement;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Reader\Word2007\AbstractPart;
use PhpOffice\PhpWord\Shared\XMLReader;
use PhpOffice\PhpWord\Shared\ZipArchive;

/**
 * Reader for Word2007.
 *
 * @since 0.8.0
 *
 * @todo watermark, checkbox, toc
 * @todo Partly done: image, object
 */
class Word2007 extends AbstractReader implements ReaderInterface
{
    /**
     * Loads PhpWord from file.
     *
     * @param string $docFile
     *
     * @return PhpWord
     */
    public function load($docFile)
    {
        $phpWord = new PhpWord();
        $relationships = $this->readRelationships($docFile);
        $commentRefs = [];

        $steps = [
            [
                'stepPart' => 'document',
                'stepItems' => [
                    'styles' => 'Styles',
                    'numbering' => 'Numbering',
                ],
            ],
            [
                'stepPart' => 'main',
                'stepItems' => [
                    'officeDocument' => 'Document',
                    'core-properties' => 'DocPropsCore',
                    'extended-properties' => 'DocPropsApp',
                    'custom-properties' => 'DocPropsCustom',
                ],
            ],
            [
                'stepPart' => 'document',
                'stepItems' => [
                    'endnotes' => 'Endnotes',
                    'footnotes' => 'Footnotes',
                    'settings' => 'Settings',
                    'comments' => 'Comments',
                ],
            ],
        ];

        foreach ($steps as $step) {
            $stepPart = $step['stepPart'];
            $stepItems = $step['stepItems'];
            if (!isset($relationships[$stepPart])) {
                continue;
            }
            foreach ($relationships[$stepPart] as $relItem) {
                $relType = $relItem['type'];
                if (isset($stepItems[$relType])) {
                    $partName = $stepItems[$relType];
                    $xmlFile = $relItem['target'];
                    $part = $this->readPart($phpWord, $relationships, $commentRefs, $partName, $docFile, $xmlFile);
                    $commentRefs = $part->getCommentReferences();
                }
            }
        }

        return $phpWord;
    }

    /**
     * Read document part.
     *
     * @param array<string, array<string, null|AbstractElement>> $commentRefs
     */
    private function readPart(PhpWord $phpWord, array $relationships, array $commentRefs, string $partName, string $docFile, string $xmlFile): AbstractPart
    {
        $partClass = "PhpOffice\\PhpWord\\Reader\\Word2007\\{$partName}";
        if (!class_exists($partClass)) {
            throw new Exception(sprintf('The part "%s" doesn\'t exist', $partClass));
        }

        /** @var AbstractPart $part Type hint */
        $part = new $partClass($docFile, $xmlFile);
        $part->setImageLoading($this->hasImageLoading());
        $part->setRels($relationships);
        $part->setCommentReferences($commentRefs);
        $part->read($phpWord);

        return $part;
    }

    /**
     * Read all relationship files.
     *
     * @param string $docFile
     *
     * @return array
     */
    private function readRelationships($docFile)
    {
        $relationships = [];

        // _rels/.rels
        $relationships['main'] = $this->getRels($docFile, '_rels/.rels');

        // word/_rels/*.xml.rels
        $wordRelsPath = 'word/_rels/';
        $zip = new ZipArchive();
        if ($zip->open($docFile) === true) {
            for ($i = 0; $i < $zip->numFiles; ++$i) {
                $xmlFile = $zip->getNameIndex($i);
                if ((substr($xmlFile, 0, strlen($wordRelsPath))) == $wordRelsPath && (substr($xmlFile, -1)) != '/') {
                    $docPart = str_replace('.xml.rels', '', str_replace($wordRelsPath, '', $xmlFile));
                    $relationships[$docPart] = $this->getRels($docFile, $xmlFile, 'word/');
                }
            }
            $zip->close();
        }

        return $relationships;
    }

    /**
     * Get relationship array.
     *
     * @param string $docFile
     * @param string $xmlFile
     * @param string $targetPrefix
     *
     * @return array
     */
    private function getRels($docFile, $xmlFile, $targetPrefix = '')
    {
        $metaPrefix = 'http://schemas.openxmlformats.org/package/2006/relationships/metadata/';
        $officePrefix = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/';

        $rels = [];

        $xmlReader = new XMLReader();
        $xmlReader->getDomFromZip($docFile, $xmlFile);
        $nodes = $xmlReader->getElements('*');
        foreach ($nodes as $node) {
            $rId = $xmlReader->getAttribute('Id', $node);
            $type = $xmlReader->getAttribute('Type', $node);
            $target = $xmlReader->getAttribute('Target', $node);
            $mode = $xmlReader->getAttribute('TargetMode', $node);

            // Remove URL prefixes from $type to make it easier to read
            $type = str_replace($metaPrefix, '', $type);
            $type = str_replace($officePrefix, '', $type);
            $docPart = str_replace('.xml', '', $target);

            // Do not add prefix to link source
            if ($type != 'hyperlink' && $mode != 'External') {
                $target = $targetPrefix . $target;
            }

            // Push to return array
            $rels[$rId] = ['type' => $type, 'target' => $target, 'docPart' => $docPart, 'targetMode' => $mode];
        }
        ksort($rels);

        return $rels;
    }
}
