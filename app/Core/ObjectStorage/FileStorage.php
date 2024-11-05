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
        $this->createFolder($key);

        if (file_put_contents($this->baseDir.DIRECTORY_SEPARATOR.$key, $blob) === false) {
            throw new ObjectStorageException('Unable to write the file: '.$this->baseDir.DIRECTORY_SEPARATOR.$key);
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
     * @param  string  $src_filename
     * @param  string  $key
     * @return boolean
     */
    public function moveFile($src_filename, $key)
    {
        $this->createFolder($key);
        $dst_filename = $this->baseDir.DIRECTORY_SEPARATOR.$key;

        if (! rename($src_filename, $dst_filename)) {
            throw new ObjectStorageException('Unable to move the file: '.$src_filename.' to '.$dst_filename);
        }

        return true;
    }

    /**
     * Move uploaded file to object storage
     *
     * @access public
     * @param  string  $filename
     * @param  string  $key
     * @return boolean
     */
    public function moveUploadedFile($filename, $key)
    {
        $this->createFolder($key);
        return move_uploaded_file($filename, $this->baseDir.DIRECTORY_SEPARATOR.$key);
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

    /**
     * Create object folder
     *
     * @access private
     * @throws ObjectStorageException
     * @param  string  $key
     */
    private function createFolder($key)
    {
        $folder = strpos($key, DIRECTORY_SEPARATOR) !== false ? $this->baseDir.DIRECTORY_SEPARATOR.dirname($key) : $this->baseDir;

        if (! is_dir($folder) && ! mkdir($folder, 0755, true)) {
            throw new ObjectStorageException('Unable to create folder: '.$folder);
        }
    }

    /**
     * Get real file path
     *
     * @access private
     * @throws ObjectStorageException
     * @param  string  $key
     * @return string
     */
    private function getRealFilePath($key)
    {
        $realFilePath = realpath($this->baseDir.DIRECTORY_SEPARATOR.$key);

        if ($realFilePath === false) {
            throw new ObjectStorageException('Invalid path: '.$key);
        }

        if (strpos($realFilePath, $this->baseDir) !== 0) {
            throw new ObjectStorageException('File is not in base directory: '.$realFilePath);
        }

        if (! file_exists($realFilePath)) {
            throw new ObjectStorageException('File not found: '.$realFilePath);
        }

        return $realFilePath;
    }
}
