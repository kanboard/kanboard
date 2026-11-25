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

namespace PhpOffice\PhpWord\Writer\Word2007\Part;

use PhpOffice\PhpWord\Exception\Exception;
use PhpOffice\PhpWord\Shared\XMLWriter;

/**
 * Word2007 main relationship writer: _rels/.rels.
 *
 * @since 0.10.0
 */
class Rels extends AbstractPart
{
    /**
     * Write part.
     *
     * @return string
     */
    public function write()
    {
        $xmlRels = [
            'docProps/core.xml' => 'package/2006/relationships/metadata/core-properties',
            'docProps/app.xml' => 'officeDocument/2006/relationships/extended-properties',
            'docProps/custom.xml' => 'officeDocument/2006/relationships/custom-properties',
            'word/document.xml' => 'officeDocument/2006/relationships/officeDocument',
        ];
        $xmlWriter = $this->getXmlWriter();
        $this->writeRels($xmlWriter, $xmlRels);

        return $xmlWriter->getData();
    }

    /**
     * Write relationships.
     *
     * @param array $xmlRels
     * @param array $mediaRels
     * @param int $relId
     */
    protected function writeRels(XMLWriter $xmlWriter, $xmlRels = [], $mediaRels = [], $relId = 1): void
    {
        $xmlWriter->startDocument('1.0', 'UTF-8', 'yes');
        $xmlWriter->startElement('Relationships');
        $xmlWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/package/2006/relationships');

        // XML files relationships
        foreach ($xmlRels as $target => $type) {
            $this->writeRel($xmlWriter, $relId++, $type, $target);
        }

        // Media relationships
        foreach ($mediaRels as $mediaRel) {
            $this->writeMediaRel($xmlWriter, $relId++, $mediaRel);
        }

        $xmlWriter->endElement(); // Relationships
    }

    /**
     * Write media relationships.
     *
     * @param int $relId
     * @param array $mediaRel
     */
    private function writeMediaRel(XMLWriter $xmlWriter, $relId, $mediaRel): void
    {
        $typePrefix = 'officeDocument/2006/relationships/';
        $typeMapping = ['image' => 'image', 'object' => 'oleObject', 'link' => 'hyperlink'];
        $targetMapping = ['image' => 'media/', 'object' => 'embeddings/'];

        $mediaType = $mediaRel['type'];
        $type = $typeMapping[$mediaType] ?? $mediaType;
        $targetPrefix = $targetMapping[$mediaType] ?? '';
        $target = $mediaRel['target'];
        $targetMode = ($type == 'hyperlink') ? 'External' : '';

        $this->writeRel($xmlWriter, $relId, $typePrefix . $type, $targetPrefix . $target, $targetMode);
    }

    /**
     * Write individual rels entry.
     *
     * Format:
     * <Relationship Id="rId..." Type="http://..." Target="....xml" TargetMode="..." />
     *
     * @param int $relId Relationship ID
     * @param string $type Relationship type
     * @param string $target Relationship target
     * @param string $targetMode Relationship target mode
     */
    private function writeRel(XMLWriter $xmlWriter, $relId, $type, $target, $targetMode = ''): void
    {
        if ($type != '' && $target != '') {
            if (strpos($relId, 'rId') === false) {
                $relId = 'rId' . $relId;
            }
            $xmlWriter->startElement('Relationship');
            $xmlWriter->writeAttribute('Id', $relId);
            $xmlWriter->writeAttribute('Type', 'http://schemas.openxmlformats.org/' . $type);
            $xmlWriter->writeAttribute('Target', $target);
            if ($targetMode != '') {
                $xmlWriter->writeAttribute('TargetMode', $targetMode);
            }
            $xmlWriter->endElement();
        } else {
            throw new Exception('Invalid parameters passed.');
        }
    }
}
