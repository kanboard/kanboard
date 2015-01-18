<?php

namespace Controller;

use Model\SubTask as SubTaskModel;

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
     * Dashboard for the current user
     *
     * @access public
     */
    public function index()
    {
        $status = array(SubTaskModel::STATUS_TODO, SubTaskModel::STATUS_INPROGRESS);
        $user_id = $this->userSession->getId();
        $projects = $this->projectPermission->getMemberProjects($user_id);
        $project_ids = array_keys($projects);

        $task_paginator = $this->paginator
            ->setUrl('app', 'index', array('pagination' => 'tasks'))
            ->setMax(10)
            ->setOrder('tasks.id')
            ->setQuery($this->taskFinder->getUserQuery($user_id))
            ->calculateOnlyIf($this->request->getStringParam('pagination') === 'tasks');

        $subtask_paginator = $this->paginator
            ->setUrl('app', 'index', array('pagination' => 'subtasks'))
            ->setMax(10)
            ->setOrder('tasks.id')
            ->setQuery($this->subTask->getUserQuery($user_id, $status))
            ->calculateOnlyIf($this->request->getStringParam('pagination') === 'subtasks');

        $project_paginator = $this->paginator
            ->setUrl('app', 'index', array('pagination' => 'projects'))
            ->setMax(10)
            ->setOrder('name')
            ->setQuery($this->project->getQueryColumnStats($project_ids))
            ->calculateOnlyIf($this->request->getStringParam('pagination') === 'projects');

        $this->response->html($this->template->layout('app/dashboard', array(
            'title' => t('Dashboard'),
            'board_selector' => $this->projectPermission->getAllowedProjects($user_id),
            'events' => $this->projectActivity->getProjects($project_ids, 10),
            'task_paginator' => $task_paginator,
            'subtask_paginator' => $subtask_paginator,
            'project_paginator' => $project_paginator,
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
        else {
            $this->response->html(
                $this->template->markdown($payload['text'])
            );
        }
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
}
