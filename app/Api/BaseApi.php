<?php

namespace Kanboard\Api;

use JsonRPC\Exception\AccessDeniedException;
use Kanboard\Core\Base;

/**
 * Base class
 *
 * @package  Kanboard\Api
 * @author   Frederic Guillot
 */
abstract class BaseApi extends Base
{
    public function checkProjectPermission($project_id)
    {
        if ($this->userSession->isLogged() && ! $this->projectPermissionModel->isUserAllowed($project_id, $this->userSession->getId())) {
            throw new AccessDeniedException('Permission denied');
        }
    }

    public function checkTaskPermission($task_id)
    {
        if ($this->userSession->isLogged()) {
            $this->checkProjectPermission($this->taskFinderModel->getProjectId($task_id));
        }
    }

    protected function formatTask($task)
    {
        if (! empty($task)) {
            $task['url'] = $this->helper->url->to('TaskViewController', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']), '', true);
            $task['color'] = $this->colorModel->getColorProperties($task['color_id']);
        }

        return $task;
    }

    protected function formatTasks($tasks)
    {
        if (! empty($tasks)) {
            foreach ($tasks as &$task) {
                $task = $this->formatTask($task);
            }
        }

        return $tasks;
    }

    protected function formatProject($project)
    {
        if (! empty($project)) {
            $project['url'] = array(
                'board' => $this->helper->url->to('BoardViewController', 'show', array('project_id' => $project['id']), '', true),
                'calendar' => $this->helper->url->to('CalendarController', 'show', array('project_id' => $project['id']), '', true),
                'list' => $this->helper->url->to('TaskListController', 'show', array('project_id' => $project['id']), '', true),
            );
        }

        return $project;
    }

    protected function formatProjects($projects)
    {
        if (! empty($projects)) {
            foreach ($projects as &$project) {
                $project = $this->formatProject($project);
            }
        }

        return $projects;
    }
}
