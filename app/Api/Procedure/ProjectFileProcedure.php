<?php

namespace Kanboard\Api\Procedure;

use Kanboard\Api\Authorization\ProjectAuthorization;
use Kanboard\Core\ObjectStorage\ObjectStorageException;

/**
 * Project File API controller
 *
 * @package  Kanboard\Api\Procedure
 * @author   Frederic Guillot
 */
class ProjectFileProcedure extends BaseProcedure
{
    public function getProjectFile($project_id, $file_id)
    {
        ProjectAuthorization::getInstance($this->container)->check($this->getClassName(), 'getProjectFile', $project_id);
        return $this->projectFileModel->getById($file_id);
    }

    public function getAllProjectFiles($project_id)
    {
        ProjectAuthorization::getInstance($this->container)->check($this->getClassName(), 'getAllProjectFiles', $project_id);
        return $this->projectFileModel->getAll($project_id);
    }

    public function downloadProjectFile($project_id, $file_id)
    {
        ProjectAuthorization::getInstance($this->container)->check($this->getClassName(), 'downloadProjectFile', $project_id);

        try {
            $file = $this->projectFileModel->getById($file_id);

            if (! empty($file)) {
                return base64_encode($this->objectStorage->get($file['path']));
            }
        } catch (ObjectStorageException $e) {
            $this->logger->error($e->getMessage());
        }

        return '';
    }

    public function createProjectFile($project_id, $filename, $blob)
    {
        ProjectAuthorization::getInstance($this->container)->check($this->getClassName(), 'createProjectFile', $project_id);

        try {
            return $this->projectFileModel->uploadContent($project_id, $filename, $blob);
        } catch (ObjectStorageException $e) {
            $this->logger->error(__METHOD__.': '.$e->getMessage());
            return false;
        }
    }

    public function removeProjectFile($project_id, $file_id)
    {
        ProjectAuthorization::getInstance($this->container)->check($this->getClassName(), 'removeProjectFile', $project_id);
        return $this->projectFileModel->remove($file_id);
    }

    public function removeAllProjectFiles($project_id)
    {
        ProjectAuthorization::getInstance($this->container)->check($this->getClassName(), 'removeAllProjectFiles', $project_id);
        return $this->projectFileModel->removeAll($project_id);
    }
}
