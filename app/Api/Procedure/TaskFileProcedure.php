<?php

namespace Kanboard\Api\Procedure;

use Kanboard\Api\Authorization\ProjectAuthorization;
use Kanboard\Api\Authorization\TaskAuthorization;
use Kanboard\Api\Authorization\TaskFileAuthorization;
use Kanboard\Core\ObjectStorage\ObjectStorageException;

/**
 * Task File API controller
 *
 * @package  Kanboard\Api\Procedure
 * @author   Frederic Guillot
 */
class TaskFileProcedure extends BaseProcedure
{
    public function getTaskFile($file_id)
    {
        TaskFileAuthorization::getInstance($this->container)->check($this->getClassName(), 'getTaskFile', $file_id);
        return $this->taskFileModel->getById($file_id);
    }

    public function getAllTaskFiles($task_id)
    {
        TaskAuthorization::getInstance($this->container)->check($this->getClassName(), 'getAllTaskFiles', $task_id);
        return $this->taskFileModel->getAll($task_id);
    }

    public function downloadTaskFile($file_id)
    {
        TaskFileAuthorization::getInstance($this->container)->check($this->getClassName(), 'downloadTaskFile', $file_id);

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
        ProjectAuthorization::getInstance($this->container)->check($this->getClassName(), 'createTaskFile', $project_id);

        try {
            return $this->taskFileModel->uploadContent($task_id, $filename, $blob);
        } catch (ObjectStorageException $e) {
            $this->logger->error(__METHOD__.': '.$e->getMessage());
            return false;
        }
    }

    public function removeTaskFile($file_id)
    {
        TaskFileAuthorization::getInstance($this->container)->check($this->getClassName(), 'removeTaskFile', $file_id);
        return $this->taskFileModel->remove($file_id);
    }

    public function removeAllTaskFiles($task_id)
    {
        TaskAuthorization::getInstance($this->container)->check($this->getClassName(), 'removeAllTaskFiles', $task_id);
        return $this->taskFileModel->removeAll($task_id);
    }
}
