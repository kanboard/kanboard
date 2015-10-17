<?php

namespace Kanboard\Model;

use Kanboard\Event\FileEvent;
use Kanboard\Core\Tool;
use Kanboard\Core\ObjectStorage\ObjectStorageException;

/**
 * File model
 *
 * @package  model
 * @author   Frederic Guillot
 */
class File extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'files';

    /**
     * Events
     *
     * @var string
     */
    const EVENT_CREATE = 'file.create';

    /**
     * Get a file by the id
     *
     * @access public
     * @param  integer   $file_id    File id
     * @return array
     */
    public function getById($file_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $file_id)->findOne();
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
            $file = $this->getbyId($file_id);
            $this->objectStorage->remove($file['path']);

            if ($file['is_image'] == 1) {
                $this->objectStorage->remove($this->getThumbnailPath($file['path']));
            }

            return $this->db->table(self::TABLE)->eq('id', $file['id'])->remove();
        } catch (ObjectStorageException $e) {
            $this->logger->error($e->getMessage());
            return false;
        }
    }

    /**
     * Remove all files for a given task
     *
     * @access public
     * @param  integer   $task_id    Task id
     * @return bool
     */
    public function removeAll($task_id)
    {
        $file_ids = $this->db->table(self::TABLE)->eq('task_id', $task_id)->asc('id')->findAllByColumn('id');
        $results = array();

        foreach ($file_ids as $file_id) {
            $results[] = $this->remove($file_id);
        }

        return ! in_array(false, $results, true);
    }

    /**
     * Create a file entry in the database
     *
     * @access public
     * @param  integer  $task_id    Task id
     * @param  string   $name       Filename
     * @param  string   $path       Path on the disk
     * @param  integer  $size       File size
     * @return bool|integer
     */
    public function create($task_id, $name, $path, $size)
    {
        $result = $this->db->table(self::TABLE)->save(array(
            'task_id' => $task_id,
            'name' => substr($name, 0, 255),
            'path' => $path,
            'is_image' => $this->isImage($name) ? 1 : 0,
            'size' => $size,
            'user_id' => $this->userSession->getId() ?: 0,
            'date' => time(),
        ));

        if ($result) {
            $this->container['dispatcher']->dispatch(
                self::EVENT_CREATE,
                new FileEvent(array('task_id' => $task_id, 'name' => $name))
            );

            return (int) $this->db->getLastId();
        }

        return false;
    }

    /**
     * Get PicoDb query to get all files
     *
     * @access public
     * @return \PicoDb\Table
     */
    public function getQuery()
    {
        return $this->db
            ->table(self::TABLE)
            ->columns(
                self::TABLE.'.id',
                self::TABLE.'.name',
                self::TABLE.'.path',
                self::TABLE.'.is_image',
                self::TABLE.'.task_id',
                self::TABLE.'.date',
                self::TABLE.'.user_id',
                self::TABLE.'.size',
                User::TABLE.'.username',
                User::TABLE.'.name as user_name'
            )
            ->join(User::TABLE, 'id', 'user_id')
            ->asc(self::TABLE.'.name');
    }

    /**
     * Get all files for a given task
     *
     * @access public
     * @param  integer   $task_id    Task id
     * @return array
     */
    public function getAll($task_id)
    {
        return $this->getQuery()->eq('task_id', $task_id)->findAll();
    }

    /**
     * Get all images for a given task
     *
     * @access public
     * @param  integer   $task_id    Task id
     * @return array
     */
    public function getAllImages($task_id)
    {
        return $this->getQuery()->eq('task_id', $task_id)->eq('is_image', 1)->findAll();
    }

    /**
     * Get all files without images for a given task
     *
     * @access public
     * @param  integer   $task_id    Task id
     * @return array
     */
    public function getAllDocuments($task_id)
    {
        return $this->getQuery()->eq('task_id', $task_id)->eq('is_image', 0)->findAll();
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
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        switch ($extension) {
            case 'jpeg':
            case 'jpg':
            case 'png':
            case 'gif':
                return true;
        }

        return false;
    }

    /**
     * Return the image mimetype based on the file extension
     *
     * @access public
     * @param  $filename
     * @return string
     */
    public function getImageMimeType($filename)
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        switch ($extension) {
            case 'jpeg':
            case 'jpg':
                return 'image/jpeg';
            case 'png':
                return 'image/png';
            case 'gif':
                return 'image/gif';
            default:
                return 'image/jpeg';
        }
    }

    /**
     * Generate the path for a new filename
     *
     * @access public
     * @param  integer   $project_id    Project id
     * @param  integer   $task_id       Task id
     * @param  string    $filename      Filename
     * @return string
     */
    public function generatePath($project_id, $task_id, $filename)
    {
        return $project_id.DIRECTORY_SEPARATOR.$task_id.DIRECTORY_SEPARATOR.hash('sha1', $filename.time());
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
     * Handle file upload
     *
     * @access public
     * @param  integer  $project_id    Project id
     * @param  integer  $task_id       Task id
     * @param  string   $form_name     File form name
     * @return bool
     */
    public function uploadFiles($project_id, $task_id, $form_name)
    {
        try {
            if (empty($_FILES[$form_name])) {
                return false;
            }

            foreach ($_FILES[$form_name]['error'] as $key => $error) {
                if ($error == UPLOAD_ERR_OK && $_FILES[$form_name]['size'][$key] > 0) {
                    $original_filename = $_FILES[$form_name]['name'][$key];
                    $uploaded_filename = $_FILES[$form_name]['tmp_name'][$key];
                    $destination_filename = $this->generatePath($project_id, $task_id, $original_filename);

                    if ($this->isImage($original_filename)) {
                        $this->generateThumbnailFromFile($uploaded_filename, $destination_filename);
                    }

                    $this->objectStorage->moveUploadedFile($uploaded_filename, $destination_filename);

                    $this->create(
                        $task_id,
                        $original_filename,
                        $destination_filename,
                        $_FILES[$form_name]['size'][$key]
                    );
                }
            }

            return true;
        } catch (ObjectStorageException $e) {
            $this->logger->error($e->getMessage());
            return false;
        }
    }

    /**
     * Handle screenshot upload
     *
     * @access public
     * @param  integer  $project_id   Project id
     * @param  integer  $task_id      Task id
     * @param  string   $blob         Base64 encoded image
     * @return bool|integer
     */
    public function uploadScreenshot($project_id, $task_id, $blob)
    {
        $original_filename = e('Screenshot taken %s', dt('%B %e, %Y at %k:%M %p', time())).'.png';
        return $this->uploadContent($project_id, $task_id, $original_filename, $blob);
    }

    /**
     * Handle file upload (base64 encoded content)
     *
     * @access public
     * @param  integer  $project_id            Project id
     * @param  integer  $task_id               Task id
     * @param  string   $original_filename     Filename
     * @param  string   $blob                  Base64 encoded file
     * @return bool|integer
     */
    public function uploadContent($project_id, $task_id, $original_filename, $blob)
    {
        try {
            $data = base64_decode($blob);

            if (empty($data)) {
                return false;
            }

            $destination_filename = $this->generatePath($project_id, $task_id, $original_filename);
            $this->objectStorage->put($destination_filename, $data);

            if ($this->isImage($original_filename)) {
                $this->generateThumbnailFromData($destination_filename, $data);
            }

            return $this->create(
                $task_id,
                $original_filename,
                $destination_filename,
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
        $temp_filename = tempnam(sys_get_temp_dir(), 'datafile');

        file_put_contents($temp_filename, $data);
        $this->generateThumbnailFromFile($temp_filename, $destination_filename);
        unlink($temp_filename);
    }

    /**
     * Generate thumbnail from a blob
     *
     * @access public
     * @param  string   $uploaded_filename
     * @param  string   $destination_filename
     */
    public function generateThumbnailFromFile($uploaded_filename, $destination_filename)
    {
        $thumbnail_filename = tempnam(sys_get_temp_dir(), 'thumbnail');
        Tool::generateThumbnail($uploaded_filename, $thumbnail_filename);
        $this->objectStorage->moveFile($thumbnail_filename, $this->getThumbnailPath($destination_filename));
    }
}
