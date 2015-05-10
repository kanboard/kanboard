<?php

namespace Controller;

use Model\Subtask as SubtaskModel;
use Model\Task as TaskModel;

/**
 * Application controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class App extends Base
{
    /**
     * Check if the user is connected
     *
     * @access public
     */
    public function status()
    {
        $this->response->text('OK');
    }

    /**
     * User dashboard view for admins
     *
     * @access public
     */
    public function dashboard()
    {
        $this->index($this->request->getIntegerParam('user_id'), 'dashboard');
    }

    /**
     * Dashboard for the current user
     *
     * @access public
     */
    public function index($user_id = 0, $action = 'index')
    {
        $status = array(SubTaskModel::STATUS_TODO, SubtaskModel::STATUS_INPROGRESS);
        $user_id = $user_id ?: $this->userSession->getId();
        $projects = $this->projectPermission->getActiveMemberProjects($user_id);
        $project_ids = array_keys($projects);

        $task_paginator = $this->paginator
            ->setUrl('app', $action, array('pagination' => 'tasks', 'user_id' => $user_id))
            ->setMax(10)
            ->setOrder('tasks.id')
            ->setQuery($this->taskFinder->getUserQuery($user_id))
            ->calculateOnlyIf($this->request->getStringParam('pagination') === 'tasks');

        $subtask_paginator = $this->paginator
            ->setUrl('app', $action, array('pagination' => 'subtasks', 'user_id' => $user_id))
            ->setMax(10)
            ->setOrder('tasks.id')
            ->setQuery($this->subtask->getUserQuery($user_id, $status))
            ->calculateOnlyIf($this->request->getStringParam('pagination') === 'subtasks');

        $project_paginator = $this->paginator
            ->setUrl('app', $action, array('pagination' => 'projects', 'user_id' => $user_id))
            ->setMax(10)
            ->setOrder('name')
            ->setQuery($this->project->getQueryColumnStats($project_ids))
            ->calculateOnlyIf($this->request->getStringParam('pagination') === 'projects');

        $this->response->html($this->template->layout('app/dashboard', array(
            'title' => t('Dashboard'),
            'board_selector' => $this->projectPermission->getAllowedProjects($user_id),
            'events' => $this->projectActivity->getProjects($project_ids, 5),
            'task_paginator' => $task_paginator,
            'subtask_paginator' => $subtask_paginator,
            'project_paginator' => $project_paginator,
            'user_id' => $user_id,
        )));
    }

    /**
     * Render Markdown text and reply with the HTML Code
     *
     * @access public
     */
    public function preview()
    {
        $payload = $this->request->getJson();

        if (empty($payload['text'])) {
            $this->response->html('<p>'.t('Nothing to preview...').'</p>');
        }

        $this->response->html($this->template->markdown($payload['text']));
    }

    /**
     * Colors stylesheet
     *
     * @access public
     */
    public function colors()
    {
        $this->response->css($this->color->getCss());
    }

    /**
     * Task autocompletion (Ajax)
     *
     * @access public
     */
    public function autocomplete()
    {
        $search = $this->request->getStringParam('term');

        $filter = $this->taskFilter
            ->create()
            ->filterByProjects($this->projectPermission->getActiveMemberProjectIds($this->userSession->getId()))
            ->excludeTasks(array($this->request->getIntegerParam('exclude_task_id')));

        // Search by task id or by title
        if (ctype_digit($search)) {
            $filter->filterById($search);
        }
        else {
            $filter->filterByTitle($search);
        }

        $this->response->json($filter->toAutoCompletion());
    }
}
