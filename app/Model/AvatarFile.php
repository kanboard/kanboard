<?php

namespace Kanboard\Model;

use Exception;

/**
 * Avatar File
 *
 * @package  model
 * @author   Frederic Guillot
 */
class AvatarFile extends Base
{
    /**
     * Path prefix
     *
     * @var string
     */
    const PATH_PREFIX = 'avatars';

    /**
     * Get image filename
     *
     * @access public
     * @param  integer $user_id
     * @return string
     */
    public function getFilename($user_id)
    {
        return $this->db->table(User::TABLE)->eq('id', $user_id)->findOneColumn('avatar_path');
    }

    /**
     * Add avatar in the user profile
     *
     * @access public
     * @param  integer  $user_id    Foreign key
     * @param  string   $path       Path on the disk
     * @return bool
     */
    public function create($user_id, $path)
    {
        $result = $this->db->table(User::TABLE)->eq('id', $user_id)->update(array(
            'avatar_path' => $path,
        ));

        $this->userSession->refresh($user_id);

        return $result;
    }

    /**
     * Remove avatar from the user profile
     *
     * @access public
     * @param  integer  $user_id    Foreign key
     * @return bool
     */
    public function remove($user_id)
    {
        try {
            $this->objectStorage->remove($this->getFilename($user_id));
            $result = $this->db->table(User::TABLE)->eq('id', $user_id)->update(array('avatar_path' => ''));
            $this->userSession->refresh($user_id);
            return $result;
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            return false;
        }
    }

    /**
     * Upload avatar image
     *
     * @access public
     * @param  integer $user_id
     * @param  array   $file
     */
    public function uploadFile($user_id, array $file)
    {
        try {
            if ($file['error'] == UPLOAD_ERR_OK && $file['size'] > 0) {
                $destination_filename = $this->generatePath($user_id, $file['name']);
                $this->objectStorage->moveUploadedFile($file['tmp_name'], $destination_filename);
                $this->create($user_id, $destination_filename);
            } else {
                throw new Exception('File not uploaded: '.var_export($file['error'], true));
            }

        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * Generate the path for a new filename
     *
     * @access public
     * @param  integer   $user_id
     * @param  string    $filename
     * @return string
     */
    public function generatePath($user_id, $filename)
    {
        return implode(DIRECTORY_SEPARATOR, array(self::PATH_PREFIX, $user_id, hash('sha1', $filename.time())));
    }
}
