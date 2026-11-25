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
 * Word2007 part relationship writer: word/_rels/(header|footer|footnotes|endnotes)*.xml.rels.
 *
 * @since 0.11.0
 */
class RelsPart extends Rels
{
    /**
     * Media relationships.
     *
     * @var array
     */
    private $media = [];

    /**
     * Write part.
     *
     * @return string
     */
    public function write()
    {
        $xmlWriter = $this->getXmlWriter();
        $this->writeRels($xmlWriter, [], $this->media);

        return $xmlWriter->getData();
    }

    /**
     * Set media.
     *
     * @param array $media
     *
     * @return self
     */
    public function setMedia($media)
    {
        $this->media = $media;

        return $this;
    }
}
