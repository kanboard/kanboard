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

namespace PhpOffice\PhpWord\Writer\EPub3\Style;

use PhpOffice\PhpWord\Shared\XMLWriter;
use PhpOffice\PhpWord\Writer\AbstractWriter;

/**
 * Abstract class for EPub3 styles.
 */
abstract class AbstractStyle
{
    /**
     * Parent writer.
     *
     * @var AbstractWriter
     */
    protected $parentWriter;

    /**
     * XML Writer.
     *
     * @var XMLWriter
     */
    protected $xmlWriter;

    /**
     * Set parent writer.
     */
    public function setParentWriter(AbstractWriter $writer): self
    {
        $this->parentWriter = $writer;

        return $this;
    }

    /**
     * Set XML Writer.
     */
    public function setXmlWriter(XMLWriter $writer): self
    {
        $this->xmlWriter = $writer;

        return $this;
    }

    /**
     * Get parent writer.
     */
    public function getParentWriter(): AbstractWriter
    {
        return $this->parentWriter;
    }

    /**
     * Write style content.
     */
    abstract public function write(): string;
}
