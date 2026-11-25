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

use PhpOffice\PhpWord\Metadata\DocInfo;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\XMLReader;

/**
 * Custom properties reader.
 *
 * @since 0.11.0
 */
class DocPropsCustom extends AbstractPart
{
    /**
     * Read custom document properties.
     */
    public function read(PhpWord $phpWord): void
    {
        $xmlReader = new XMLReader();
        $xmlReader->getDomFromZip($this->docFile, $this->xmlFile);
        $docProps = $phpWord->getDocInfo();

        $nodes = $xmlReader->getElements('*');
        if ($nodes->length > 0) {
            foreach ($nodes as $node) {
                $propertyName = $xmlReader->getAttribute('name', $node);
                $attributeNode = $xmlReader->getElement('*', $node);
                $attributeType = $attributeNode->nodeName;
                $attributeValue = $attributeNode->nodeValue;
                $attributeValue = DocInfo::convertProperty($attributeValue, $attributeType);
                $attributeType = DocInfo::convertPropertyType($attributeType);
                $docProps->setCustomProperty($propertyName, $attributeValue, $attributeType);
            }
        }
    }
}
