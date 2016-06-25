<?php

namespace Kanboard\Api;

use Kanboard\Core\ObjectStorage\ObjectStorageException;

/**
 * Task File API controller
 *
 * @package  Kanboard\Api
 * @author   Frederic Guillot
 */
class TaskFileApi extends BaseApi
{
    public function getTaskFile($file_id)
    {
        return $this->taskFileModel->getById($file_id);
    }

    public function getAllTaskFiles($task_id)
    {
        return $this->taskFileModel->getAll($task_id);
    }

    public function downloadTaskFile($file_id)
    {
        try {
            $file = $this->taskFileModel->getById($file_id);

            if (! empty($file)) {
                return base64_encode($this->objectStorage->get($file['path']));
            }
        } catch (ObjectStorageException $e) {
            $this->logger->error($e->getMessage());
        }

        return '';
    }

    public function createTaskFile($project_id, $task_id, $filename, $blob)
    {
        try {
            return $this->taskFileModel->uploadContent($task_id, $filename, $blob);
        } catch (ObjectStorageException $e) {
            $this->logger->error($e->getMessage());
            return false;
        }
    }

    public function removeTaskFile($file_id)
    {
        return $this->taskFileModel->remove($file_id);
    }

    public function removeAllTaskFiles($task_id)
    {
        return $this->taskFileModel->removeAll($task_id);
    }
}
