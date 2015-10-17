<?php

namespace Kanboard\Api;

use Kanboard\Core\ObjectStorage\ObjectStorageException;

/**
 * File API controller
 *
 * @package  api
 * @author   Frederic Guillot
 */
class File extends \Kanboard\Core\Base
{
    public function getFile($file_id)
    {
        return $this->file->getById($file_id);
    }

    public function getAllFiles($task_id)
    {
        return $this->file->getAll($task_id);
    }

    public function downloadFile($file_id)
    {
        try {
            $file = $this->file->getById($file_id);

            if (! empty($file)) {
                return base64_encode($this->objectStorage->get($file['path']));
            }
        } catch (ObjectStorageException $e) {
            $this->logger->error($e->getMessage());
        }

        return '';
    }

    public function createFile($project_id, $task_id, $filename, $blob)
    {
        return $this->file->uploadContent($project_id, $task_id, $filename, $blob);
    }

    public function removeFile($file_id)
    {
        return $this->file->remove($file_id);
    }

    public function removeAllFiles($task_id)
    {
        return $this->file->removeAll($task_id);
    }
}
