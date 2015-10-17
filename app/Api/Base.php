<?php

namespace Kanboard\Api;

use JsonRPC\AuthenticationFailure;
use JsonRPC\AccessDeniedException;

/**
 * Base class
 *
 * @package  api
 * @author   Frederic Guillot
 */
abstract class Base extends \Kanboard\Core\Base
{
    private $user_allowed_procedures = array(
        'getMe',
        'getMyDashboard',
        'getMyActivityStream',
        'createMyPrivateProject',
        'getMyProjectsList',
        'getMyProjects',
        'getMyOverdueTasks',
    );

    private $both_allowed_procedures = array(
        'getTimezone',
        'getVersion',
        'getDefaultTaskColor',
        'getDefaultTaskColors',
        'getColorList',
        'getProjectById',
        'getTask',
        'getTaskByReference',
        'getAllTasks',
        'openTask',
        'closeTask',
        'moveTaskPosition',
        'createTask',
        'updateTask',
        'getBoard',
        'getProjectActivity',
        'getOverdueTasksByProject',
    );

    public function checkProcedurePermission($is_user, $procedure)
    {
        $is_both_procedure = in_array($procedure, $this->both_allowed_procedures);
        $is_user_procedure = in_array($procedure, $this->user_allowed_procedures);

        if ($is_user && ! $is_both_procedure && ! $is_user_procedure) {
            throw new AccessDeniedException('Permission denied');
        } elseif (! $is_user && ! $is_both_procedure && $is_user_procedure) {
            throw new AccessDeniedException('Permission denied');
        }

        $this->logger->debug('API call: '.$procedure);
    }

    public function checkProjectPermission($project_id)
    {
        if ($this->userSession->isLogged() && ! $this->projectPermission->isUserAllowed($project_id, $this->userSession->getId())) {
            throw new AccessDeniedException('Permission denied');
        }
    }

    public function checkTaskPermission($task_id)
    {
        if ($this->userSession->isLogged()) {
            $this->checkProjectPermission($this->taskFinder->getProjectId($task_id));
        }
    }

    protected function formatTask($task)
    {
        if (! empty($task)) {
            $task['url'] = $this->helper->url->to('task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']), '', true);
            $task['color'] = $this->color->getColorProperties($task['color_id']);
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
                'board' => $this->helper->url->to('board', 'show', array('project_id' => $project['id']), '', true),
                'calendar' => $this->helper->url->to('calendar', 'show', array('project_id' => $project['id']), '', true),
                'list' => $this->helper->url->to('listing', 'show', array('project_id' => $project['id']), '', true),
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
