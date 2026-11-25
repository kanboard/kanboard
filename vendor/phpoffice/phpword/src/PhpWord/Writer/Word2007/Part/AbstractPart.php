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
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\Shared\XMLWriter;
use PhpOffice\PhpWord\Writer\AbstractWriter;

/**
 * Word2007 writer part abstract class.
 */
abstract class AbstractPart
{
    /**
     * Parent writer.
     *
     * @var AbstractWriter
     */
    protected $parentWriter;

    /**
     * @var string Date format
     */
    protected $dateFormat = 'Y-m-d\TH:i:sP';

    /**
     * Write part.
     *
     * @return string
     */
    abstract public function write();

    /**
     * Set parent writer.
     */
    public function setParentWriter(?AbstractWriter $writer = null): void
    {
        $this->parentWriter = $writer;
    }

    /**
     * Get parent writer.
     *
     * @return AbstractWriter
     */
    public function getParentWriter()
    {
        if (null !== $this->parentWriter) {
            return $this->parentWriter;
        }

        throw new Exception('No parent WriterInterface assigned.');
    }

    /**
     * Get XML Writer.
     *
     * @return XMLWriter
     */
    protected function getXmlWriter()
    {
        $useDiskCaching = false;
        if (null !== $this->parentWriter) {
            if ($this->parentWriter->isUseDiskCaching()) {
                $useDiskCaching = true;
            }
        }
        if ($useDiskCaching) {
            return new XMLWriter(XMLWriter::STORAGE_DISK, $this->parentWriter->getDiskCachingDirectory(), Settings::hasCompatibility());
        }

        return new XMLWriter(XMLWriter::STORAGE_MEMORY, './', Settings::hasCompatibility());
    }

    /**
     * Write an XML text, this will call text() or writeRaw() depending on the value of Settings::isOutputEscapingEnabled().
     *
     * @param string $content The text string to write
     *
     * @return bool Returns true on success or false on failure
     */
    protected function writeText($content)
    {
        if (Settings::isOutputEscapingEnabled()) {
            return $this->getXmlWriter()->text($content);
        }

        return $this->getXmlWriter()->writeRaw($content);
    }
}
