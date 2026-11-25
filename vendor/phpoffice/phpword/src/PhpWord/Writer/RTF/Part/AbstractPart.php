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

namespace PhpOffice\PhpWord\Writer\RTF\Part;

use PhpOffice\PhpWord\Escaper\Rtf;
use PhpOffice\PhpWord\Exception\Exception;
use PhpOffice\PhpWord\Writer\AbstractWriter;

/**
 * @since 0.11.0
 */
abstract class AbstractPart
{
    /**
     * @var \PhpOffice\PhpWord\Writer\RTF
     */
    private $parentWriter;

    /**
     * @var \PhpOffice\PhpWord\Escaper\EscaperInterface
     */
    protected $escaper;

    public function __construct()
    {
        $this->escaper = new Rtf();
    }

    /**
     * @return string
     */
    abstract public function write();

    /**
     * @param \PhpOffice\PhpWord\Writer\RTF $writer
     */
    public function setParentWriter(?AbstractWriter $writer = null): void
    {
        $this->parentWriter = $writer;
    }

    /**
     * @return \PhpOffice\PhpWord\Writer\RTF
     */
    public function getParentWriter()
    {
        if ($this->parentWriter !== null) {
            return $this->parentWriter;
        }

        throw new Exception('No parent WriterInterface assigned.');
    }
}
