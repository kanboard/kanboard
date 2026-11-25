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

use PhpOffice\PhpWord\Exception\CopyFileException;
use PhpOffice\PhpWord\Exception\Exception;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\Shared\ZipArchive;

/**
 * Abstract writer class.
 *
 * @since 0.10.0
 */
abstract class AbstractWriter implements WriterInterface
{
    /**
     * PHPWord object.
     *
     * @var PhpWord
     */
    protected $phpWord;

    /**
     * Part name and file name pairs.
     *
     * @var array
     */
    protected $parts = [];

    /**
     * Individual writers.
     *
     * @var array
     */
    protected $writerParts = [];

    /**
     * Paths to store media files.
     *
     * @var array
     */
    protected $mediaPaths = ['image' => '', 'object' => ''];

    /**
     * Use disk caching.
     *
     * @var bool
     */
    private $useDiskCaching = false;

    /**
     * Disk caching directory.
     *
     * @var string
     */
    private $diskCachingDirectory = './';

    /**
     * Temporary directory.
     *
     * @var string
     */
    private $tempDir = '';

    /**
     * Original file name.
     *
     * @var string
     */
    private $originalFilename;

    /**
     * Temporary file name.
     *
     * @var string
     */
    private $tempFilename;

    /**
     * Get PhpWord object.
     *
     * @return PhpWord
     */
    public function getPhpWord()
    {
        if (null !== $this->phpWord) {
            return $this->phpWord;
        }

        throw new Exception('No PhpWord assigned.');
    }

    /**
     * Set PhpWord object.
     *
     * @return self
     */
    public function setPhpWord(?PhpWord $phpWord = null)
    {
        $this->phpWord = $phpWord;

        return $this;
    }

    /**
     * Get writer part.
     *
     * @param string $partName Writer part name
     *
     * @return mixed
     */
    public function getWriterPart($partName = '')
    {
        if ($partName != '' && isset($this->writerParts[strtolower($partName)])) {
            return $this->writerParts[strtolower($partName)];
        }

        return null;
    }

    /**
     * Get use disk caching status.
     *
     * @return bool
     */
    public function isUseDiskCaching()
    {
        return $this->useDiskCaching;
    }

    /**
     * Set use disk caching status.
     *
     * @param bool $value
     * @param string $directory
     *
     * @return self
     */
    public function setUseDiskCaching($value = false, $directory = null)
    {
        $this->useDiskCaching = $value;

        if (null !== $directory) {
            if (is_dir($directory)) {
                $this->diskCachingDirectory = $directory;
            } else {
                throw new Exception("Directory does not exist: $directory");
            }
        }

        return $this;
    }

    /**
     * Get disk caching directory.
     *
     * @return string
     */
    public function getDiskCachingDirectory()
    {
        return $this->diskCachingDirectory;
    }

    /**
     * Get temporary directory.
     *
     * @return string
     */
    public function getTempDir()
    {
        return $this->tempDir;
    }

    /**
     * Set temporary directory.
     *
     * @param string $value
     *
     * @return self
     */
    public function setTempDir($value)
    {
        if (!is_dir($value)) {
            mkdir($value);
        }
        $this->tempDir = $value;

        return $this;
    }

    /**
     * Get temporary file name.
     *
     * If $filename is php://output or php://stdout, make it a temporary file
     *
     * @param string $filename
     *
     * @return string
     */
    protected function getTempFile($filename)
    {
        // Temporary directory
        $this->setTempDir(Settings::getTempDir() . uniqid('/PHPWordWriter_', true) . '/');

        // Temporary file
        $this->originalFilename = $filename;
        if (strpos(strtolower($filename), 'php://') === 0) {
            $filename = tempnam(Settings::getTempDir(), 'PhpWord');
            if (false === $filename) {
                $filename = $this->originalFilename; // @codeCoverageIgnore
            } // @codeCoverageIgnore
        }
        $this->tempFilename = $filename;

        return $this->tempFilename;
    }

    /**
     * Cleanup temporary file.
     */
    protected function cleanupTempFile(): void
    {
        if ($this->originalFilename != $this->tempFilename) {
            // @codeCoverageIgnoreStart
            // Can't find any test case. Uncomment when found.
            if (false === copy($this->tempFilename, $this->originalFilename)) {
                throw new CopyFileException($this->tempFilename, $this->originalFilename);
            }
            // @codeCoverageIgnoreEnd
            @unlink($this->tempFilename);
        }

        $this->clearTempDir();
    }

    /**
     * Clear temporary directory.
     */
    protected function clearTempDir(): void
    {
        if (is_dir($this->tempDir)) {
            $this->deleteDir($this->tempDir);
        }
    }

    /**
     * Get ZipArchive object.
     *
     * @param string $filename
     *
     * @return ZipArchive
     */
    protected function getZipArchive($filename)
    {
        // Remove any existing file
        if (file_exists($filename)) {
            unlink($filename);
        }

        // Try opening the ZIP file
        $zip = new ZipArchive();

        // @codeCoverageIgnoreStart
        // Can't find any test case. Uncomment when found.
        if ($zip->open($filename, ZipArchive::OVERWRITE) !== true) {
            if ($zip->open($filename, ZipArchive::CREATE) !== true) {
                throw new \Exception("Could not open '{$filename}' for writing.");
            }
        }
        // @codeCoverageIgnoreEnd

        return $zip;
    }

    /**
     * Open file for writing.
     *
     * @since 0.11.0
     *
     * @param string $filename
     *
     * @return resource
     */
    protected function openFile($filename)
    {
        $filename = $this->getTempFile($filename);
        $fileHandle = fopen($filename, 'wb');
        // @codeCoverageIgnoreStart
        // Can't find any test case. Uncomment when found.
        if ($fileHandle === false) {
            throw new \Exception("Could not open '{$filename}' for writing.");
        }
        // @codeCoverageIgnoreEnd

        return $fileHandle;
    }

    /**
     * Write content to file.
     *
     * @since 0.11.0
     *
     * @param resource $fileHandle
     * @param string $content
     */
    protected function writeFile($fileHandle, $content): void
    {
        fwrite($fileHandle, $content);
        fclose($fileHandle);
        $this->cleanupTempFile();
    }

    /**
     * Add files to package.
     *
     * @param mixed $elements
     */
    protected function addFilesToPackage(ZipArchive $zip, $elements): void
    {
        foreach ($elements as $element) {
            $type = $element['type']; // image|object|link

            // Skip nonregistered types and set target
            if (!isset($this->mediaPaths[$type])) {
                continue;
            }
            $target = $this->mediaPaths[$type] . $element['target'];

            // Retrive GD image content or get local media
            if (isset($element['isMemImage']) && $element['isMemImage']) {
                $imageContents = $element['imageString'];
                $zip->addFromString($target, $imageContents);
            } else {
                $this->addFileToPackage($zip, $element['source'], $target);
            }
        }
    }

    /**
     * Add file to package.
     *
     * Get the actual source from an archive image.
     *
     * @param ZipArchive $zipPackage
     * @param string $source
     * @param string $target
     */
    protected function addFileToPackage($zipPackage, $source, $target): void
    {
        $isArchive = strpos($source, 'zip://') !== false;
        $actualSource = null;
        if ($isArchive) {
            $source = substr($source, 6);
            [$zipFilename, $imageFilename] = explode('#', $source);

            $zip = new ZipArchive();
            if ($zip->open($zipFilename) !== false) {
                if ($zip->locateName($imageFilename)) {
                    $zip->extractTo($this->getTempDir(), $imageFilename);
                    $actualSource = $this->getTempDir() . DIRECTORY_SEPARATOR . $imageFilename;
                }
            }
            $zip->close();
        } else {
            $actualSource = $source;
        }

        if (null !== $actualSource) {
            $zipPackage->addFile($actualSource, $target);
        }
    }

    /**
     * Delete directory.
     *
     * @param string $dir
     */
    private function deleteDir($dir): void
    {
        foreach (scandir($dir) as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            } elseif (is_file($dir . '/' . $file)) {
                unlink($dir . '/' . $file);
            } elseif (is_dir($dir . '/' . $file)) {
                $this->deleteDir($dir . '/' . $file);
            }
        }

        rmdir($dir);
    }
}
