<?php

namespace Kanboard\Api\Procedure;

/**
 * Me API controller
 *
 * @package  Kanboard\Api\Procedure
 * @author   Frederic Guillot
 */
class MeProcedure extends BaseProcedure
{
    public function getMe()
    {
        return $this->sessionStorage->user;
    }

    public function getMyDashboard()
    {
        $userId = $this->userSession->getId();

        return $this->taskListSubtaskAssigneeFormatter
            ->withQuery($this->taskFinderModel->getUserQuery($userId))
            ->withUserId($userId)
            ->format();
    }

    public function getMyActivityStream()
    {
        $project_ids = $this->projectPermissionModel->getActiveProjectIds($this->userSession->getId());
        return $this->helper->projectActivity->getProjectsEvents($project_ids, 100);
    }

    public function createMyPrivateProject($name, $description = null)
    {
        if ($this->configModel->get('disable_private_project', 0) == 1) {
            return false;
        }

        $values = array(
            'name' => $name,
            'description' => $description,
            'is_private' => 1,
        );

        list($valid, ) = $this->projectValidator->validateCreation($values);
        return $valid ? $this->projectModel->create($values, $this->userSession->getId(), true) : false;
    }

    public function getMyProjectsList()
    {
        return (object) $this->projectUserRoleModel->getProjectsByUser($this->userSession->getId());
    }

    public function getMyOverdueTasks()
    {
        return $this->taskFinderModel->getOverdueTasksByUser($this->userSession->getId());
    }

    public function getMyProjects()
    {
        $project_ids = $this->projectPermissionModel->getActiveProjectIds($this->userSession->getId());
        $projects = $this->projectModel->getAllByIds($project_ids);

        return $this->projectsApiFormatter->withProjects($projects)->format();
    }
}
