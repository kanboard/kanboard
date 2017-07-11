<?php

namespace Kanboard\Api\Procedure;

use Kanboard\Api\Authorization\ProjectAuthorization;

/**
 * Project API controller
 *
 * @package  Kanboard\Api\Procedure
 * @author   Frederic Guillot
 */
class ProjectProcedure extends BaseProcedure
{
    public function getProjectById($project_id)
    {
        ProjectAuthorization::getInstance($this->container)->check($this->getClassName(), 'getProjectById', $project_id);
        $project = $this->projectModel->getById($project_id);
        return $this->projectApiFormatter->withProject($project)->format();
    }

    public function getProjectByName($name)
    {
        $project = $this->projectModel->getByName($name);
        ProjectAuthorization::getInstance($this->container)->check($this->getClassName(), 'getProjectByName', $project['id']);
        return $this->projectApiFormatter->withProject($project)->format();
    }

    public function getProjectByIdentifier($identifier)
    {
        $project = $this->projectModel->getByIdentifier($identifier);
        ProjectAuthorization::getInstance($this->container)->check($this->getClassName(), 'getProjectByIdentifier', $project['id']);
        return $this->projectApiFormatter->withProject($project)->format();
    }

    public function getProjectByEmail($email)
    {
        $project = $this->projectModel->getByEmail($email);
        ProjectAuthorization::getInstance($this->container)->check($this->getClassName(), 'getProjectByEmail', $project['id']);
        return $this->projectApiFormatter->withProject($project)->format();
    }

    public function getAllProjects()
    {
        return $this->projectsApiFormatter->withProjects($this->projectModel->getAll())->format();
    }

    public function removeProject($project_id)
    {
        ProjectAuthorization::getInstance($this->container)->check($this->getClassName(), 'removeProject', $project_id);
        return $this->projectModel->remove($project_id);
    }

    public function enableProject($project_id)
    {
        ProjectAuthorization::getInstance($this->container)->check($this->getClassName(), 'enableProject', $project_id);
        return $this->projectModel->enable($project_id);
    }

    public function disableProject($project_id)
    {
        ProjectAuthorization::getInstance($this->container)->check($this->getClassName(), 'disableProject', $project_id);
        return $this->projectModel->disable($project_id);
    }

    public function enableProjectPublicAccess($project_id)
    {
        ProjectAuthorization::getInstance($this->container)->check($this->getClassName(), 'enableProjectPublicAccess', $project_id);
        return $this->projectModel->enablePublicAccess($project_id);
    }

    public function disableProjectPublicAccess($project_id)
    {
        ProjectAuthorization::getInstance($this->container)->check($this->getClassName(), 'disableProjectPublicAccess', $project_id);
        return $this->projectModel->disablePublicAccess($project_id);
    }

    public function getProjectActivities(array $project_ids)
    {
        foreach ($project_ids as $project_id) {
            ProjectAuthorization::getInstance($this->container)
                ->check($this->getClassName(), 'getProjectActivities', $project_id);
        }

        return $this->helper->projectActivity->getProjectsEvents($project_ids);
    }

    public function getProjectActivity($project_id)
    {
        ProjectAuthorization::getInstance($this->container)->check($this->getClassName(), 'getProjectActivity', $project_id);
        return $this->helper->projectActivity->getProjectEvents($project_id);
    }

    public function createProject($name, $description = null, $owner_id = 0, $identifier = null)
    {
        $values = $this->filterValues(array(
            'name' => $name,
            'description' => $description,
            'identifier' => $identifier,
        ));

        list($valid, ) = $this->projectValidator->validateCreation($values);
        return $valid ? $this->projectModel->create($values, $owner_id, $this->userSession->isLogged()) : false;
    }

    public function updateProject($project_id, $name = null, $description = null, $owner_id = null, $identifier = null)
    {
        ProjectAuthorization::getInstance($this->container)->check($this->getClassName(), 'updateProject', $project_id);

        $values = $this->filterValues(array(
            'id' => $project_id,
            'name' => $name,
            'description' => $description,
            'owner_id' => $owner_id,
            'identifier' => $identifier,
        ));

        list($valid, ) = $this->projectValidator->validateModification($values);
        return $valid && $this->projectModel->update($values);
    }
}
