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

namespace PhpOffice\PhpWord\Shared;

use Exception;
use ReturnTypeWillChange;

/**
 * XMLWriter.
 *
 * @method bool endElement()
 * @method mixed flush(bool $empty = null)
 * @method bool openMemory()
 * @method string outputMemory(bool $flush = null)
 * @method bool setIndent(bool $indent)
 * @method bool startDocument(string $version = 1.0, string $encoding = null, string $standalone = null)
 * @method bool startElement(string $name)
 * @method bool text(string $content)
 * @method bool writeCData(string $content)
 * @method bool writeComment(string $content)
 * @method bool writeElement(string $name, string $content = null)
 * @method bool writeRaw(string $content)
 */
class XMLWriter extends \XMLWriter
{
    /** Temporary storage method */
    const STORAGE_MEMORY = 1;
    const STORAGE_DISK = 2;

    /**
     * Temporary filename.
     *
     * @var string
     */
    private $tempFileName = '';

    /**
     * Create a new \PhpOffice\PhpWord\Shared\XMLWriter instance.
     *
     * @param int $pTemporaryStorage Temporary storage location
     * @param string $pTemporaryStorageDir Temporary storage folder
     * @param bool $compatibility
     */
    public function __construct($pTemporaryStorage = self::STORAGE_MEMORY, $pTemporaryStorageDir = null, $compatibility = false)
    {
        // Open temporary storage
        if ($pTemporaryStorage == self::STORAGE_MEMORY) {
            $this->openMemory();
        } else {
            if (!$pTemporaryStorageDir || !is_dir($pTemporaryStorageDir)) {
                $pTemporaryStorageDir = sys_get_temp_dir();
            }
            // Create temporary filename
            $this->tempFileName = @tempnam($pTemporaryStorageDir, 'xml');

            // Open storage
            $this->openUri($this->tempFileName);
        }

        if ($compatibility) {
            $this->setIndent(false);
            $this->setIndentString('');
        } else {
            $this->setIndent(true);
            $this->setIndentString('  ');
        }
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        // Unlink temporary files
        if (empty($this->tempFileName)) {
            return;
        }
        if (PHP_OS != 'WINNT' && @unlink($this->tempFileName) === false) {
            throw new Exception('The file ' . $this->tempFileName . ' could not be deleted.');
        }
    }

    /**
     * Get written data.
     *
     * @return string
     */
    public function getData()
    {
        if ($this->tempFileName == '') {
            return $this->outputMemory(true);
        }

        $this->flush();

        return file_get_contents($this->tempFileName);
    }

    /**
     * Write simple element and attribute(s) block.
     *
     * There are two options:
     * 1. If the `$attributes` is an array, then it's an associative array of attributes
     * 2. If not, then it's a simple attribute-value pair
     *
     * @param string $element
     * @param array|string $attributes
     * @param string $value
     */
    public function writeElementBlock($element, $attributes, $value = null): void
    {
        $this->startElement($element);
        if (!is_array($attributes)) {
            $attributes = [$attributes => $value];
        }
        foreach ($attributes as $attribute => $value) {
            $this->writeAttribute($attribute, $value);
        }
        $this->endElement();
    }

    /**
     * Write element if ...
     *
     * @param bool $condition
     * @param string $element
     * @param string $attribute
     * @param mixed $value
     */
    public function writeElementIf($condition, $element, $attribute = null, $value = null): void
    {
        if ($condition == true) {
            if (null === $attribute) {
                $this->writeElement($element, $value);
            } else {
                $this->startElement($element);
                $this->writeAttribute($attribute, $value);
                $this->endElement();
            }
        }
    }

    /**
     * Write attribute if ...
     *
     * @param bool $condition
     * @param string $attribute
     * @param mixed $value
     */
    public function writeAttributeIf($condition, $attribute, $value): void
    {
        if ($condition == true) {
            $this->writeAttribute($attribute, $value);
        }
    }

    /**
     * @param string $name
     * @param mixed $value
     *
     * @return bool
     */
    #[ReturnTypeWillChange]
    public function writeAttribute($name, $value)
    {
        if (is_float($value)) {
            $value = json_encode($value);
        }

        return parent::writeAttribute($name, $value ?? '');
    }
}
