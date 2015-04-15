<?php

namespace Model;

use Event\FileEvent;

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
        $file = $this->getbyId($file_id);

        if (! empty($file) && @unlink(FILES_DIR.$file['path'])) {
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
     * @param  integer  $size       File size
     * @return bool
     */
    public function create($task_id, $name, $path, $is_image, $size)
    {
        $this->container['dispatcher']->dispatch(
            self::EVENT_CREATE,
            new FileEvent(array('task_id' => $task_id, 'name' => $name))
        );

        return $this->db->table(self::TABLE)->save(array(
            'task_id' => $task_id,
            'name' => $name,
            'path' => $path,
            'is_image' => $is_image ? '1' : '0',
            'size' => $size,
            'user_id' => $this->userSession->getId(),
            'date' => time(),
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
            ->eq('task_id', $task_id)
            ->asc(self::TABLE.'.name')
            ->findAll();
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
            ->eq('task_id', $task_id)
            ->eq('is_image', 1)
            ->asc(self::TABLE.'.name')
            ->findAll();
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
            ->eq('task_id', $task_id)
            ->eq('is_image', 0)
            ->asc(self::TABLE.'.name')
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
        if (! is_dir(FILES_DIR)) {
            if (! mkdir(FILES_DIR, 0755, true)) {
                die('Unable to create the upload directory: "'.FILES_DIR.'"');
            }
        }

        if (! is_writable(FILES_DIR)) {
            die('The directory "'.FILES_DIR.'" must be writeable by your webserver user');
        }
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

                    @mkdir(FILES_DIR.dirname($destination_filename), 0755, true);

                    if (@move_uploaded_file($uploaded_filename, FILES_DIR.$destination_filename)) {

                        $result[] = $this->create(
                            $task_id,
                            $original_filename,
                            $destination_filename,
                            $this->isImage($original_filename),
                            $_FILES[$form_name]['size'][$key]
                        );
                    }
                }
            }
        }

        return count(array_unique($result)) === 1;
    }

    /**
     * Handle screenshot upload
     *
     * @access public
     * @param  integer  $project_id   Project id
     * @param  integer  $task_id      Task id
     * @param  string   $blob         Base64 encoded image
     * @return bool
     */
    public function uploadScreenshot($project_id, $task_id, $blob)
    {
        $data = base64_decode($blob);

        if (empty($data)) {
            return false;
        }

        $original_filename = e('Screenshot taken %s', dt('%B %e, %Y at %k:%M %p', time()));
        $destination_filename = $this->generatePath($project_id, $task_id, $original_filename);

        @mkdir(FILES_DIR.dirname($destination_filename), 0755, true);
        @file_put_contents(FILES_DIR.$destination_filename, $data);

        return $this->create(
            $task_id,
            $original_filename,
            $destination_filename,
            true,
            strlen($data)
        );
    }

    /**
     * Generate a jpeg thumbnail from an image (output directly the image)
     *
     * @access public
     * @param  string    $filename         Source image
     * @param  integer   $resize_width     Desired image width
     * @param  integer   $resize_height    Desired image height
     */
    public function generateThumbnail($filename, $resize_width, $resize_height)
    {
        $metadata = getimagesize($filename);
        $src_width = $metadata[0];
        $src_height = $metadata[1];
        $dst_y = 0;
        $dst_x = 0;

        if (empty($metadata['mime'])) {
            return;
        }

        if ($resize_width == 0 && $resize_height == 0) {
            $resize_width = 100;
            $resize_height = 100;
        }

        if ($resize_width > 0 && $resize_height == 0) {
            $dst_width = $resize_width;
            $dst_height = floor($src_height * ($resize_width / $src_width));
            $dst_image = imagecreatetruecolor($dst_width, $dst_height);
        }
        elseif ($resize_width == 0 && $resize_height > 0) {
            $dst_width = floor($src_width * ($resize_height / $src_height));
            $dst_height = $resize_height;
            $dst_image = imagecreatetruecolor($dst_width, $dst_height);
        }
        else {

            $src_ratio = $src_width / $src_height;
            $resize_ratio = $resize_width / $resize_height;

            if ($src_ratio <= $resize_ratio) {
                $dst_width = $resize_width;
                $dst_height = floor($src_height * ($resize_width / $src_width));

                $dst_y = ($dst_height - $resize_height) / 2 * (-1);
            }
            else {
                $dst_width = floor($src_width * ($resize_height / $src_height));
                $dst_height = $resize_height;

                $dst_x = ($dst_width - $resize_width) / 2 * (-1);
            }

            $dst_image = imagecreatetruecolor($resize_width, $resize_height);
        }

        switch ($metadata['mime']) {
            case 'image/jpeg':
            case 'image/jpg':
                $src_image = imagecreatefromjpeg($filename);
                break;
            case 'image/png':
                $src_image = imagecreatefrompng($filename);
                break;
            case 'image/gif':
                $src_image = imagecreatefromgif($filename);
                break;
            default:
                return;
        }

        imagecopyresampled($dst_image, $src_image, $dst_x, $dst_y, 0, 0, $dst_width, $dst_height, $src_width, $src_height);
        imagejpeg($dst_image);
    }
}
