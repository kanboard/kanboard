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

namespace PhpOffice\PhpWord\Reader;

use PhpOffice\PhpWord\Exception\Exception;

/**
 * Reader abstract class.
 *
 * @since 0.8.0
 *
 * @codeCoverageIgnore Abstract class
 */
abstract class AbstractReader implements ReaderInterface
{
    /**
     * Read data only?
     *
     * @var bool
     */
    protected $readDataOnly = true;

    /**
     * File pointer.
     *
     * @var bool|resource
     */
    protected $fileHandle;

    /**
     * Load images.
     *
     * @var bool
     */
    protected $imageLoading = true;

    /**
     * Read data only?
     *
     * @return bool
     */
    public function isReadDataOnly()
    {
        // return $this->readDataOnly;
        return true;
    }

    /**
     * Set read data only.
     *
     * @param bool $value
     *
     * @return self
     */
    public function setReadDataOnly($value = true)
    {
        $this->readDataOnly = $value;

        return $this;
    }

    public function hasImageLoading(): bool
    {
        return $this->imageLoading;
    }

    public function setImageLoading(bool $value): self
    {
        $this->imageLoading = $value;

        return $this;
    }

    /**
     * Open file for reading.
     *
     * @param string $filename
     *
     * @return resource
     */
    protected function openFile($filename)
    {
        // Check if file exists
        if (!file_exists($filename) || !is_readable($filename)) {
            throw new Exception("Could not open $filename for reading! File does not exist.");
        }

        // Open file
        $this->fileHandle = fopen($filename, 'rb');
        if ($this->fileHandle === false) {
            throw new Exception("Could not open file $filename for reading.");
        }
    }

    /**
     * Can the current ReaderInterface read the file?
     *
     * @param string $filename
     *
     * @return bool
     */
    public function canRead($filename)
    {
        // Check if file exists
        try {
            $this->openFile($filename);
        } catch (Exception $e) {
            return false;
        }
        if (is_resource($this->fileHandle)) {
            fclose($this->fileHandle);
        }

        return true;
    }
}
