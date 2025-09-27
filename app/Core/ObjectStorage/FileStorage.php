<?php

namespace Kanboard\Core\ObjectStorage;

/**
 * Local File Storage
 *
 * @package  ObjectStorage
 * @author   Frederic Guillot
 */
class FileStorage implements ObjectStorageInterface
{
    /**
     * Base directory
     *
     * @access private
     * @var string
     */
    private $baseDir = '';

    /**
     * Constructor
     *
     * @access public
     * @param  string  $baseDir
     */
    public function __construct($baseDir)
    {
        $realBaseDir = realpath($baseDir);

        if ($realBaseDir === false) {
            throw new ObjectStorageException('Invalid base folder: '.$baseDir);
        }

        if (! is_dir($realBaseDir)) {
            throw new ObjectStorageException('Base folder is not a directory: '.$baseDir);
        }

        $this->baseDir = $realBaseDir;
    }

    /**
     * Fetch object contents
     *
     * @access public
     * @throws ObjectStorageException
     * @param  string  $key
     * @return string
     */
    public function get($key)
    {
        return file_get_contents($this->getRealFilePath($key));
    }

    /**
     * Save object
     *
     * @access public
     * @throws ObjectStorageException
     * @param  string  $key
     * @param  string  $blob
     */
    public function put($key, &$blob)
    {
        $filename = $this->getSanitizedFilePath($key);
        $this->createFolder($key);

        if (file_put_contents($filename, $blob) === false) {
            throw new ObjectStorageException('Unable to write the file: '.$filename);
        }
    }

    /**
     * Output directly object content
     *
     * @access public
     * @throws ObjectStorageException
     * @param  string  $key
     */
    public function output($key)
    {
        readfile($this->getRealFilePath($key));
    }

    /**
     * Move local file to object storage
     *
     * @access public
     * @throws ObjectStorageException
     * @param  string  $srcFilename
     * @param  string  $key
     * @return boolean
     */
    public function moveFile($srcFilename, $key)
    {
        if (! file_exists($srcFilename)) {
            throw new ObjectStorageException('Source file does not exist: '.$srcFilename);
        }

        $dstFilename = $this->getSanitizedFilePath($key);
        $this->createFolder($key);

        if (! rename($srcFilename, $dstFilename)) {
            throw new ObjectStorageException('Unable to move the file: '.$srcFilename.' to '.$dstFilename);
        }

        return true;
    }

    /**
     * Move uploaded file to object storage
     *
     * @access public
     * @param  string  $srcFilename
     * @param  string  $key
     * @return boolean
     */
    public function moveUploadedFile($srcFilename, $key)
    {
        if (! file_exists($srcFilename)) {
            throw new ObjectStorageException('Source file does not exist: '.$srcFilename);
        }

        $dstFilename = $this->getSanitizedFilePath($key);
        $this->createFolder($key);

        return move_uploaded_file($srcFilename, $dstFilename);
    }

    /**
     * Remove object
     *
     * @access public
     * @param  string  $key
     * @return boolean
     */
    public function remove($key)
    {
        $filename = $this->getRealFilePath($key);
        $result = unlink($filename);

        // Remove parent folder if empty
        $parentFolder = dirname($filename);
        $files = glob($parentFolder.DIRECTORY_SEPARATOR.'*');

        if ($files !== false && is_dir($parentFolder) && count($files) === 0) {
            rmdir($parentFolder);
        }

        return $result;
    }

    private function createFolder(string $key)
    {
        $folder = strpos($key, DIRECTORY_SEPARATOR) !== false ? $this->baseDir.DIRECTORY_SEPARATOR.dirname($key) : $this->baseDir;

        if (! is_dir($folder) && ! mkdir($folder, 0o755, true)) {
            throw new ObjectStorageException('Unable to create folder: '.$folder);
        }
    }

    private function getRealFilePath(string $key): string
    {
        $filename = $this->baseDir.DIRECTORY_SEPARATOR.$key;

        // Resolve the real path and make sure the file exists
        $realFilePath = realpath($filename);
        if ($realFilePath === false) {
            throw new ObjectStorageException('Invalid file path: '.$filename);
        }

        $this->validateBasePath($realFilePath);

        return $realFilePath;
    }

    public function getSanitizedFilePath(string $key): string
    {
        $filename = $this->baseDir.DIRECTORY_SEPARATOR.$key;
        $sanitizedKey = sanitize_path($filename);

        if ($sanitizedKey === false) {
            throw new ObjectStorageException('Invalid file path: '.$key);
        }

        $this->validateBasePath($sanitizedKey);

        return $sanitizedKey;
    }

    private function validateBasePath(string $filePath)
    {
        if (strpos($filePath, $this->baseDir) !== 0) {
            throw new ObjectStorageException('File '.$filePath.' is not in base directory: '.$this->baseDir);
        }
    }
}
