<?php

namespace Kanboard\Model;

use Exception;
use Kanboard\Core\Base;
use Kanboard\Core\Thumbnail;
use Kanboard\Core\ObjectStorage\ObjectStorageException;

/**
 * Base File Model
 *
 * @package  Kanboard\Model
 * @author   Frederic Guillot
 */
abstract class FileModel extends Base
{
    /**
     * Get the table
     *
     * @abstract
     * @access protected
     * @return string
     */
    abstract protected function getTable();

    /**
     * Define the foreign key
     *
     * @abstract
     * @access protected
     * @return string
     */
    abstract protected function getForeignKey();

    /**
     * Get the path prefix
     *
     * @abstract
     * @access protected
     * @return string
     */
    abstract protected function getPathPrefix();

    /**
     * Fire file creation event
     *
     * @abstract
     * @access protected
     * @param  integer $file_id
     */
    abstract protected function fireCreationEvent($file_id);

    /**
     * Get PicoDb query to get all files
     *
     * @access protected
     * @return \PicoDb\Table
     */
    protected function getQuery()
    {
        return $this->db
            ->table($this->getTable())
            ->columns(
                $this->getTable().'.id',
                $this->getTable().'.name',
                $this->getTable().'.path',
                $this->getTable().'.is_image',
                $this->getTable().'.'.$this->getForeignKey(),
                $this->getTable().'.date',
                $this->getTable().'.user_id',
                $this->getTable().'.size',
                UserModel::TABLE.'.username',
                UserModel::TABLE.'.name as user_name'
            )
            ->join(UserModel::TABLE, 'id', 'user_id')
            ->asc($this->getTable().'.name');
    }

    /**
     * Get a file by the id
     *
     * @access public
     * @param  integer   $file_id    File id
     * @return array
     */
    public function getById($file_id)
    {
        return $this->db->table($this->getTable())->eq('id', $file_id)->findOne();
    }

    /**
     * Get all files
     *
     * @access public
     * @param  integer   $id
     * @return array
     */
    public function getAll($id)
    {
        return $this->getQuery()->eq($this->getForeignKey(), $id)->findAll();
    }

    /**
     * Get all images
     *
     * @access public
     * @param  integer   $id
     * @return array
     */
    public function getAllImages($id)
    {
        return $this->getQuery()->eq($this->getForeignKey(), $id)->eq('is_image', 1)->findAll();
    }

    /**
     * Get all files without images
     *
     * @access public
     * @param  integer   $id
     * @return array
     */
    public function getAllDocuments($id)
    {
        return $this->getQuery()->eq($this->getForeignKey(), $id)->eq('is_image', 0)->findAll();
    }

    /**
     * Create a file entry in the database
     *
     * @access public
     * @param  integer $foreign_key_id Foreign key
     * @param  string  $name           Filename
     * @param  string  $path           Path on the disk
     * @param  integer $size           File size
     * @return bool|integer
     */
    public function create($foreign_key_id, $name, $path, $size)
    {
        $values = array(
            $this->getForeignKey() => $foreign_key_id,
            'name' => substr($name, 0, 255),
            'path' => $path,
            'is_image' => $this->isImage($name) ? 1 : 0,
            'size' => $size,
            'user_id' => $this->userSession->getId() ?: 0,
            'date' => time(),
        );

        $result = $this->db->table($this->getTable())->insert($values);

        if ($result) {
            $file_id = (int) $this->db->getLastId();
            $this->fireCreationEvent($file_id);
            return $file_id;
        }

        return false;
    }

    /**
     * Remove all files
     *
     * @access public
     * @param  integer   $id
     * @return bool
     */
    public function removeAll($id)
    {
        $file_ids = $this->db->table($this->getTable())->eq($this->getForeignKey(), $id)->asc('id')->findAllByColumn('id');
        $results = array();

        foreach ($file_ids as $file_id) {
            $results[] = $this->remove($file_id);
        }

        return ! in_array(false, $results, true);
    }

    /**
     * Remove a file
     *
     * @access public
     * @param  integer   $file_id    File id
     * @return bool
     */
    public function remove($file_id)
    {
        try {
            $file = $this->getById($file_id);
            $this->objectStorage->remove($file['path']);

            if ($file['is_image'] == 1) {
                $this->objectStorage->remove($this->getThumbnailPath($file['path']));
            }

            return $this->db->table($this->getTable())->eq('id', $file['id'])->remove();
        } catch (ObjectStorageException $e) {
            $this->logger->error($e->getMessage());
            return false;
        }
    }

    /**
     * Check if a filename is an image (file types that can be shown as thumbnail)
     *
     * @access public
     * @param  string   $filename   Filename
     * @return bool
     */
    public function isImage($filename)
    {
        switch (get_file_extension($filename)) {
            case 'jpeg':
            case 'jpg':
            case 'png':
            case 'gif':
                return true;
        }

        return false;
    }

    /**
     * Generate the path for a thumbnails
     *
     * @access public
     * @param  string  $key  Storage key
     * @return string
     */
    public function getThumbnailPath($key)
    {
        return 'thumbnails'.DIRECTORY_SEPARATOR.$key;
    }

    /**
     * Generate the path for a new filename
     *
     * @access public
     * @param  integer   $id            Foreign key
     * @param  string    $filename      Filename
     * @return string
     */
    public function generatePath($id, $filename)
    {
        return $this->getPathPrefix().DIRECTORY_SEPARATOR.$id.DIRECTORY_SEPARATOR.hash('sha1', $filename.time());
    }

    /**
     * Upload multiple files
     *
     * @access public
     * @param  integer  $id
     * @param  array    $files
     * @return bool
     */
    public function uploadFiles($id, array $files)
    {
        try {
            if (empty($files)) {
                return false;
            }

            foreach (array_keys($files['error']) as $key) {
                $file = array(
                    'name' => $files['name'][$key],
                    'tmp_name' => $files['tmp_name'][$key],
                    'size' => $files['size'][$key],
                    'error' => $files['error'][$key],
                );

                $this->uploadFile($id, $file);
            }

            return true;
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            return false;
        }
    }

    /**
     * Upload a file
     *
     * @access public
     * @param  integer $id
     * @param  array   $file
     * @throws Exception
     */
    public function uploadFile($id, array $file)
    {
        if ($file['error'] == UPLOAD_ERR_OK && $file['size'] > 0) {
            $destination_filename = $this->generatePath($id, $file['name']);

            if ($this->isImage($file['name'])) {
                $this->generateThumbnailFromFile($file['tmp_name'], $destination_filename);
            }

            $this->objectStorage->moveUploadedFile($file['tmp_name'], $destination_filename);
            $this->create($id, $file['name'], $destination_filename, $file['size']);
        } else {
            throw new Exception('File not uploaded: '.var_export($file['error'], true));
        }
    }

    /**
     * Handle file upload (base64 encoded content)
     *
     * @access public
     * @param  integer $id
     * @param  string  $originalFilename
     * @param  string  $data
     * @param  bool    $isEncoded
     * @return bool|int
     */
    public function uploadContent($id, $originalFilename, $data, $isEncoded = true)
    {
        try {
            if ($isEncoded) {
                $data = base64_decode($data);
            }

            if (empty($data)) {
                $this->logger->error(__METHOD__.': Content upload with no data');
                return false;
            }

            $destinationFilename = $this->generatePath($id, $originalFilename);
            $this->objectStorage->put($destinationFilename, $data);

            if ($this->isImage($originalFilename)) {
                $this->generateThumbnailFromData($destinationFilename, $data);
            }

            return $this->create(
                $id,
                $originalFilename,
                $destinationFilename,
                strlen($data)
            );
        } catch (ObjectStorageException $e) {
            $this->logger->error($e->getMessage());
            return false;
        }
    }

    /**
     * Generate thumbnail from a blob
     *
     * @access public
     * @param  string   $destination_filename
     * @param  string   $data
     */
    public function generateThumbnailFromData($destination_filename, &$data)
    {
        $blob = Thumbnail::createFromString($data)
            ->resize()
            ->toString();

        $this->objectStorage->put($this->getThumbnailPath($destination_filename), $blob);
    }

    /**
     * Generate thumbnail from a local file
     *
     * @access public
     * @param  string   $uploaded_filename
     * @param  string   $destination_filename
     */
    public function generateThumbnailFromFile($uploaded_filename, $destination_filename)
    {
        $blob = Thumbnail::createFromFile($uploaded_filename)
            ->resize()
            ->toString();

        $this->objectStorage->put($this->getThumbnailPath($destination_filename), $blob);
    }
}
