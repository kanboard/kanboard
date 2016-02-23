<?php

namespace Kanboard\Api;

use Kanboard\Core\ObjectStorage\ObjectStorageException;

/**
 * File API controller
 *
 * @package  api
 * @author   Frederic Guillot
 */
class File extends Base
{
    public function getTaskFile($file_id)
    {
        return $this->taskFile->getById($file_id);
    }

    public function getAllTaskFiles($task_id)
    {
        return $this->taskFile->getAll($task_id);
    }

    public function downloadTaskFile($file_id)
    {
        try {
            $file = $this->taskFile->getById($file_id);

            if (! empty($file)) {
                return base64_encode($this->objectStorage->get($file['path']));
            }
        } catch (ObjectStorageException $e) {
            $this->logger->error($e->getMessage());
            return '';
        }
    }

    public function createTaskFile($project_id, $task_id, $filename, $blob)
    {
        try {
            return $this->taskFile->uploadContent($task_id, $filename, $blob);
        } catch (ObjectStorageException $e) {
            $this->logger->error($e->getMessage());
            return false;
        }
    }

    public function removeTaskFile($file_id)
    {
        return $this->taskFile->remove($file_id);
    }

    public function removeAllTaskFiles($task_id)
    {
        return $this->taskFile->removeAll($task_id);
    }

    // Deprecated procedures

    public function getFile($file_id)
    {
        return $this->getTaskFile($file_id);
    }

    public function getAllFiles($task_id)
    {
        return $this->getAllTaskFiles($task_id);
    }

    public function downloadFile($file_id)
    {
        return $this->downloadTaskFile($file_id);
    }

    public function createFile($project_id, $task_id, $filename, $blob)
    {
        return $this->createTaskFile($project_id, $task_id, $filename, $blob);
    }

    public function removeFile($file_id)
    {
        return $this->removeTaskFile($file_id);
    }

    public function removeAllFiles($task_id)
    {
        return $this->removeAllTaskFiles($task_id);
    }
}
