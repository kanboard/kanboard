<?php

namespace Kanboard\Core\ObjectStorage;

/**
 * Object Storage Interface
 *
 * @package  ObjectStorage
 * @author   Frederic Guillot
 */
interface ObjectStorageInterface
{
    /**
     * Fetch object contents
     *
     * @access public
     * @param  string  $key
     * @return string
     */
    public function get($key);

    /**
     * Save object
     *
     * @access public
     * @param  string  $key
     * @param  string  $blob
     */
    public function put($key, &$blob);

    /**
     * Output directly object content
     *
     * @access public
     * @param  string  $key
     */
    public function output($key);

    /**
     * Move local file to object storage
     *
     * @access public
     * @param  string  $filename
     * @param  string  $key
     * @return boolean
     */
    public function moveFile($filename, $key);

    /**
     * Move uploaded file to object storage
     *
     * @access public
     * @param  string  $filename
     * @param  string  $key
     * @return boolean
     */
    public function moveUploadedFile($filename, $key);

    /**
     * Remove object
     *
     * @access public
     * @param  string  $key
     * @return boolean
     */
    public function remove($key);
}
