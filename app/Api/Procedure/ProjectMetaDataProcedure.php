<?php

namespace Kanboard\Api\Procedure;

use Kanboard\Api\Authorization\ProjectAuthorization;

/**
 * Class ProjectMetadataProcedure
 *
 * @package Kanboard\Api\Procedure
 * @author  Frederic Guillot
 */
class ProjectMetadataProcedure extends BaseProcedure
{
    public function getProjectMetadata($project_id)
    {
        ProjectAuthorization::getInstance($this->container)->check($this->getClassName(), 'getProject', $project_id);
        return (object) $this->projectMetadataModel->getAll($project_id);
    }

    public function getProjectMetadataByName($project_id, $name)
    {
        ProjectAuthorization::getInstance($this->container)->check($this->getClassName(), 'getProject', $project_id);
        return $this->projectMetadataModel->get($project_id, $name);
    }

    public function saveProjectMetadata($project_id, array $values)
    {
        ProjectAuthorization::getInstance($this->container)->check($this->getClassName(), 'updateProject', $project_id);
        return $this->projectMetadataModel->save($project_id, $values);
    }

    public function removeProjectMetadata($project_id, $name)
    {
        ProjectAuthorization::getInstance($this->container)->check($this->getClassName(), 'updateProject', $project_id);
        return $this->projectMetadataModel->remove($project_id, $name);
    }
}
