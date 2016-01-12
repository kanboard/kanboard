<?php

namespace Kanboard\Api;

use Kanboard\Model\Subtask as SubtaskModel;

/**
 * Me API controller
 *
 * @package  api
 * @author   Frederic Guillot
 */
class Me extends Base
{
    public function getMe()
    {
        return $this->sessionStorage->user;
    }

    public function getMyDashboard()
    {
        $user_id = $this->userSession->getId();
        $projects = $this->project->getQueryColumnStats($this->projectPermission->getActiveProjectIds($user_id))->findAll();
        $tasks = $this->taskFinder->getUserQuery($user_id)->findAll();

        return array(
            'projects' => $this->formatProjects($projects),
            'tasks' => $this->formatTasks($tasks),
            'subtasks' => $this->subtask->getUserQuery($user_id, array(SubTaskModel::STATUS_TODO, SubtaskModel::STATUS_INPROGRESS))->findAll(),
        );
    }

    public function getMyActivityStream()
    {
        $project_ids = $this->projectPermission->getActiveProjectIds($this->userSession->getId());
        return $this->projectActivity->getProjects($project_ids, 100);
    }

    public function createMyPrivateProject($name, $description = null)
    {
        $values = array(
            'name' => $name,
            'description' => $description,
            'is_private' => 1,
        );

        list($valid, ) = $this->projectValidator->validateCreation($values);
        return $valid ? $this->project->create($values, $this->userSession->getId(), true) : false;
    }

    public function getMyProjectsList()
    {
        return $this->projectUserRole->getProjectsByUser($this->userSession->getId());
    }

    public function getMyOverdueTasks()
    {
        return $this->taskFinder->getOverdueTasksByUser($this->userSession->getId());
    }

    public function getMyProjects()
    {
        $project_ids = $this->projectPermission->getActiveProjectIds($this->userSession->getId());
        $projects = $this->project->getAllByIds($project_ids);

        return $this->formatProjects($projects);
    }
}
