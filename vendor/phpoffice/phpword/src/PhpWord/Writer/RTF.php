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

namespace PhpOffice\PhpWord\Writer;

use PhpOffice\PhpWord\PhpWord;

/**
 * RTF writer.
 *
 * @since 0.7.0
 */
class RTF extends AbstractWriter implements WriterInterface
{
    /**
     * Last paragraph style.
     *
     * @var mixed
     */
    private $lastParagraphStyle;

    /**
     * Create new instance.
     */
    public function __construct(?PhpWord $phpWord = null)
    {
        $this->setPhpWord($phpWord);

        $this->parts = ['Header', 'Document'];
        foreach ($this->parts as $partName) {
            $partClass = static::class . '\\Part\\' . $partName;
            if (class_exists($partClass)) {
                /** @var RTF\Part\AbstractPart $part Type hint */
                $part = new $partClass();
                $part->setParentWriter($this);
                $this->writerParts[strtolower($partName)] = $part;
            }
        }
    }

    /**
     * Save content to file.
     */
    public function save(string $filename): void
    {
        $this->writeFile($this->openFile($filename), $this->getContent());
    }

    /**
     * Get content.
     *
     * @return string
     *
     * @since 0.11.0
     */
    private function getContent()
    {
        $content = '';

        $content .= '{';
        $content .= '\rtf1' . PHP_EOL;
        $content .= $this->getWriterPart('Header')->write();
        $content .= $this->getWriterPart('Document')->write();
        $content .= '}';

        return $content;
    }

    /**
     * Get font table.
     *
     * @return array
     */
    public function getFontTable()
    {
        return $this->getWriterPart('Header')->getFontTable();
    }

    /**
     * Get color table.
     *
     * @return array
     */
    public function getColorTable()
    {
        return $this->getWriterPart('Header')->getColorTable();
    }

    /**
     * Get last paragraph style.
     *
     * @return mixed
     */
    public function getLastParagraphStyle()
    {
        return $this->lastParagraphStyle;
    }

    /**
     * Set last paragraph style.
     *
     * @param mixed $value
     */
    public function setLastParagraphStyle($value = ''): void
    {
        $this->lastParagraphStyle = $value;
    }
}
