<?php

namespace Kanboard\Api;

/**
 * Project API controller
 *
 * @package  Kanboard\Api
 * @author   Frederic Guillot
 */
class ProjectApi extends BaseApi
{
    public function getProjectById($project_id)
    {
        $this->checkProjectPermission($project_id);
        return $this->formatProject($this->projectModel->getById($project_id));
    }

    public function getProjectByName($name)
    {
        return $this->formatProject($this->projectModel->getByName($name));
    }

    public function getAllProjects()
    {
        return $this->formatProjects($this->projectModel->getAll());
    }

    public function removeProject($project_id)
    {
        return $this->projectModel->remove($project_id);
    }

    public function enableProject($project_id)
    {
        return $this->projectModel->enable($project_id);
    }

    public function disableProject($project_id)
    {
        return $this->projectModel->disable($project_id);
    }

    public function enableProjectPublicAccess($project_id)
    {
        return $this->projectModel->enablePublicAccess($project_id);
    }

    public function disableProjectPublicAccess($project_id)
    {
        return $this->projectModel->disablePublicAccess($project_id);
    }

    public function getProjectActivities(array $project_ids)
    {
        return $this->helper->projectActivity->getProjectsEvents($project_ids);
    }

    public function getProjectActivity($project_id)
    {
        $this->checkProjectPermission($project_id);
        return $this->helper->projectActivity->getProjectEvents($project_id);
    }

    public function createProject($name, $description = null)
    {
        $values = array(
            'name' => $name,
            'description' => $description
        );

        list($valid, ) = $this->projectValidator->validateCreation($values);
        return $valid ? $this->projectModel->create($values) : false;
    }

    public function updateProject($id, $name, $description = null)
    {
        $values = array(
            'id' => $id,
            'name' => $name,
            'description' => $description
        );

        list($valid, ) = $this->projectValidator->validateModification($values);
        return $valid && $this->projectModel->update($values);
    }
}
