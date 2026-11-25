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

/**
 * Word2007 document relationship writer: word/_rels/document.xml.rels.
 *
 * @since 0.11.0
 */
class RelsDocument extends Rels
{
    /**
     * Write part.
     *
     * @return string
     */
    public function write()
    {
        $xmlRels = [
            'styles.xml' => 'officeDocument/2006/relationships/styles',
            'numbering.xml' => 'officeDocument/2006/relationships/numbering',
            'settings.xml' => 'officeDocument/2006/relationships/settings',
            'theme/theme1.xml' => 'officeDocument/2006/relationships/theme',
            'webSettings.xml' => 'officeDocument/2006/relationships/webSettings',
            'fontTable.xml' => 'officeDocument/2006/relationships/fontTable',
        ];
        $xmlWriter = $this->getXmlWriter();

        /** @var \PhpOffice\PhpWord\Writer\Word2007 $parentWriter Type hint */
        $parentWriter = $this->getParentWriter();
        $this->writeRels($xmlWriter, $xmlRels, $parentWriter->getRelationships());

        return $xmlWriter->getData();
    }
}
