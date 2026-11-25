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

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\XMLReader;

/**
 * Core properties reader.
 *
 * @since 0.10.0
 */
class DocPropsCore extends AbstractPart
{
    /**
     * Property mapping.
     *
     * @var array
     */
    protected $mapping = [
        'dc:creator' => 'setCreator',
        'dc:title' => 'setTitle',
        'dc:description' => 'setDescription',
        'dc:subject' => 'setSubject',
        'cp:keywords' => 'setKeywords',
        'cp:category' => 'setCategory',
        'cp:lastModifiedBy' => 'setLastModifiedBy',
        'dcterms:created' => 'setCreated',
        'dcterms:modified' => 'setModified',
    ];

    /**
     * Callback functions.
     *
     * @var array
     */
    protected $callbacks = ['dcterms:created' => 'strtotime', 'dcterms:modified' => 'strtotime'];

    /**
     * Read core/extended document properties.
     */
    public function read(PhpWord $phpWord): void
    {
        $xmlReader = new XMLReader();
        $xmlReader->getDomFromZip($this->docFile, $this->xmlFile);

        $docProps = $phpWord->getDocInfo();

        $nodes = $xmlReader->getElements('*');
        if ($nodes->length > 0) {
            foreach ($nodes as $node) {
                if (!isset($this->mapping[$node->nodeName])) {
                    continue;
                }
                $method = $this->mapping[$node->nodeName];
                $value = $node->nodeValue == '' ? null : $node->nodeValue;
                if (isset($this->callbacks[$node->nodeName])) {
                    $value = $this->callbacks[$node->nodeName]($value);
                }
                if (method_exists($docProps, $method)) {
                    $docProps->$method($value);
                }
            }
        }
    }
}
