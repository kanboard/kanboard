<?php

namespace Model;

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
    const TABLE = 'task_has_files';

    /**
     * Directory where are stored files
     *
     * @var string
     */
    const BASE_PATH = 'data/files/';

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
        $file = $this->getbyId($file_id);

        if (! empty($file) && @unlink(self::BASE_PATH.$file['path'])) {
            return $this->db->table(self::TABLE)->eq('id', $file_id)->remove();
        }

        return false;
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
        $files = $this->getAll($task_id);

        foreach ($files as $file) {
            $this->remove($file['id']);
        }
    }

    /**
     * Create a file entry in the database
     *
     * @access public
     * @param  integer  $task_id    Task id
     * @param  string   $name       Filename
     * @param  string   $path       Path on the disk
     * @param  bool     $is_image   Image or not
     * @return bool
     */
    public function create($task_id, $name, $path, $is_image)
    {
        $this->event->trigger(self::EVENT_CREATE, array('task_id' => $task_id, 'name' => $name));

        return $this->db->table(self::TABLE)->save(array(
            'task_id' => $task_id,
            'name' => $name,
            'path' => $path,
            'is_image' => $is_image ? '1' : '0',
        ));
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
        return $this->db->table(self::TABLE)
            ->eq('task_id', $task_id)
            ->asc('name')
            ->findAll();
    }

    /**
     * Check if a filename is an image
     *
     * @access public
     * @param  string   $filename   Filename
     * @return bool
     */
    public function isImage($filename)
    {
        return getimagesize($filename) !== false;
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
     * Check if the base directory is created correctly
     *
     * @access public
     */
    public function setup()
    {
        if (! is_dir(self::BASE_PATH)) {
            if (! mkdir(self::BASE_PATH, 0755, true)) {
                die('Unable to create the upload directory: "'.self::BASE_PATH.'"');
            }
        }

        if (! is_writable(self::BASE_PATH)) {
            die('The directory "'.self::BASE_PATH.'" must be writeable by your webserver user');
        }
    }

    /**
     * Handle file upload
     *
     * @access public
     * @param  integer $project_id Project id
     * @param  integer $task_id Task id
     * @param  string $form_name File form name
     * @return bool
     */
    public function upload($project_id, $task_id, $form_name)
    {
        $this->setup();
        $result = array();

        if (! empty($_FILES[$form_name])) {

            foreach ($_FILES[$form_name]['error'] as $key => $error) {

                if ($error == UPLOAD_ERR_OK && $_FILES[$form_name]['size'][$key] > 0) {

                    $original_filename = basename($_FILES[$form_name]['name'][$key]);
                    $uploaded_filename = $_FILES[$form_name]['tmp_name'][$key];
                    $destination_filename = $this->generatePath($project_id, $task_id, $original_filename);

                    @mkdir(self::BASE_PATH.dirname($destination_filename), 0755, true);

                    if (@move_uploaded_file($uploaded_filename, self::BASE_PATH.$destination_filename)) {

                        $result[] = $this->create(
                            $task_id,
                            $original_filename,
                            $destination_filename,
                            $this->isImage(self::BASE_PATH.$destination_filename)
                        );
                    }
                }
            }
        }

        return count(array_unique($result)) === 1;
    }
}
