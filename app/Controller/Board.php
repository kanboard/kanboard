<?php

namespace Kanboard\Controller;

/**
 * Board controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class Board extends Base
{
    /**
     * Display the public version of a board
     * Access checked by a simple token, no user login, read only, auto-refresh
     *
     * @access public
     */
    public function readonly()
    {
        $token = $this->request->getStringParam('token');
        $project = $this->project->getByToken($token);

        // Token verification
        if (empty($project)) {
            $this->forbidden(true);
        }

        // Display the board with a specific layout
        $this->response->html($this->template->layout('board/view_public', array(
            'project' => $project,
            'swimlanes' => $this->board->getBoard($project['id']),
            'title' => $project['name'],
            'description' => $project['description'],
            'no_layout' => true,
            'not_editable' => true,
            'board_public_refresh_interval' => $this->config->get('board_public_refresh_interval'),
            'board_private_refresh_interval' => $this->config->get('board_private_refresh_interval'),
            'board_highlight_period' => $this->config->get('board_highlight_period'),
        )));
    }

    /**
     * Show a board for a given project
     *
     * @access public
     */
    public function show()
    {
        $params = $this->getProjectFilters('board', 'show');

        $this->response->html($this->template->layout('board/view_private', array(
            'categories_list' => $this->category->getList($params['project']['id'], false),
            'users_list' => $this->projectPermission->getMemberList($params['project']['id'], false),
            'custom_filters_list' => $this->customFilter->getAll($params['project']['id'], $this->userSession->getId()),
            'swimlanes' => $this->taskFilter->search($params['filters']['search'])->getBoard($params['project']['id']),
            'description' => $params['project']['description'],
            'board_private_refresh_interval' => $this->config->get('board_private_refresh_interval'),
            'board_highlight_period' => $this->config->get('board_highlight_period'),
        ) + $params));
    }

    /**
     * Save the board (Ajax request made by the drag and drop)
     *
     * @access public
     */
    public function save()
    {
        $project_id = $this->request->getIntegerParam('project_id');

        if (! $project_id || ! $this->request->isAjax()) {
            return $this->response->status(403);
        }

        if (! $this->projectPermission->isUserAllowed($project_id, $this->userSession->getId())) {
            $this->response->text('Forbidden', 403);
        }

        $values = $this->request->getJson();

        $result =$this->taskPosition->movePosition(
            $project_id,
            $values['task_id'],
            $values['column_id'],
            $values['position'],
            $values['swimlane_id']
        );

        if (! $result) {
            return $this->response->status(400);
        }

        $this->response->html($this->renderBoard($project_id), 201);
    }

    /**
     * Check if the board have been changed
     *
     * @access public
     */
    public function check()
    {
        if (! $this->request->isAjax()) {
            return $this->response->status(403);
        }

        $project_id = $this->request->getIntegerParam('project_id');
        $timestamp = $this->request->getIntegerParam('timestamp');

        if (! $this->projectPermission->isUserAllowed($project_id, $this->userSession->getId())) {
            $this->response->text('Forbidden', 403);
        }

        if (! $this->project->isModifiedSince($project_id, $timestamp)) {
            return $this->response->status(304);
        }

        $this->response->html($this->renderBoard($project_id));
    }

    /**
     * Reload the board with new filters
     *
     * @access public
     */
    public function reload()
    {
        if (! $this->request->isAjax()) {
            return $this->response->status(403);
        }

        $project_id = $this->request->getIntegerParam('project_id');

        if (! $this->projectPermission->isUserAllowed($project_id, $this->userSession->getId())) {
            $this->response->text('Forbidden', 403);
        }

        $values = $this->request->getJson();
        $this->userSession->setFilters($project_id, empty($values['search']) ? '' : $values['search']);

        $this->response->html($this->renderBoard($project_id));
    }

    /**
     * Get links on mouseover
     *
     * @access public
     */
    public function tasklinks()
    {
        $task = $this->getTask();
        $this->response->html($this->template->render('board/tooltip_tasklinks', array(
            'links' => $this->taskLink->getAll($task['id']),
            'task' => $task,
        )));
    }

    /**
     * Get subtasks on mouseover
     *
     * @access public
     */
    public function subtasks()
    {
        $task = $this->getTask();
        $this->response->html($this->template->render('board/tooltip_subtasks', array(
            'subtasks' => $this->subtask->getAll($task['id']),
            'task' => $task,
        )));
    }

    /**
     * Display all attachments during the task mouseover
     *
     * @access public
     */
    public function attachments()
    {
        $task = $this->getTask();

        $this->response->html($this->template->render('board/tooltip_files', array(
            'files' => $this->file->getAll($task['id']),
            'task' => $task,
        )));
    }

    /**
     * Display comments during a task mouseover
     *
     * @access public
     */
    public function comments()
    {
        $task = $this->getTask();

        $this->response->html($this->template->render('board/tooltip_comments', array(
            'comments' => $this->comment->getAll($task['id'], $this->userSession->getCommentSorting())
        )));
    }

    /**
     * Display task description
     *
     * @access public
     */
    public function description()
    {
        $task = $this->getTask();

        $this->response->html($this->template->render('board/tooltip_description', array(
            'task' => $task
        )));
    }

    /**
     * Change a task assignee directly from the board
     *
     * @access public
     */
    public function changeAssignee()
    {
        $task = $this->getTask();
        $project = $this->project->getById($task['project_id']);

        $this->response->html($this->template->render('board/popover_assignee', array(
            'values' => $task,
            'users_list' => $this->projectPermission->getMemberList($project['id']),
            'project' => $project,
        )));
    }

    /**
     * Validate an assignee modification
     *
     * @access public
     */
    public function updateAssignee()
    {
        $values = $this->request->getValues();

        list($valid, ) = $this->taskValidator->validateAssigneeModification($values);

        if ($valid && $this->taskModification->update($values)) {
            $this->session->flash(t('Task updated successfully.'));
        } else {
            $this->session->flashError(t('Unable to update your task.'));
        }

        $this->response->redirect($this->helper->url->to('board', 'show', array('project_id' => $values['project_id'])));
    }

    /**
     * Change a task category directly from the board
     *
     * @access public
     */
    public function changeCategory()
    {
        $task = $this->getTask();
        $project = $this->project->getById($task['project_id']);

        $this->response->html($this->template->render('board/popover_category', array(
            'values' => $task,
            'categories_list' => $this->category->getList($project['id']),
            'project' => $project,
        )));
    }

    /**
     * Validate a category modification
     *
     * @access public
     */
    public function updateCategory()
    {
        $values = $this->request->getValues();

        list($valid, ) = $this->taskValidator->validateCategoryModification($values);

        if ($valid && $this->taskModification->update($values)) {
            $this->session->flash(t('Task updated successfully.'));
        } else {
            $this->session->flashError(t('Unable to update your task.'));
        }

        $this->response->redirect($this->helper->url->to('board', 'show', array('project_id' => $values['project_id'])));
    }

    /**
     * Screenshot popover
     *
     * @access public
     */
    public function screenshot()
    {
        $task = $this->getTask();

        $this->response->html($this->template->render('file/screenshot', array(
            'task' => $task,
            'redirect' => 'board',
        )));
    }

    /**
     * Get recurrence information on mouseover
     *
     * @access public
     */
    public function recurrence()
    {
        $task = $this->getTask();

        $this->response->html($this->template->render('task/recurring_info', array(
            'task' => $task,
            'recurrence_trigger_list' => $this->task->getRecurrenceTriggerList(),
            'recurrence_timeframe_list' => $this->task->getRecurrenceTimeframeList(),
            'recurrence_basedate_list' => $this->task->getRecurrenceBasedateList(),
        )));
    }

    /**
     * Display swimlane description in tooltip
     *
     * @access public
     */
    public function swimlane()
    {
        $this->getProject();
        $swimlane = $this->swimlane->getById($this->request->getIntegerParam('swimlane_id'));
        $this->response->html($this->template->render('board/tooltip_description', array('task' => $swimlane)));
    }

    /**
     * Enable collapsed mode
     *
     * @access public
     */
    public function collapse()
    {
        $this->changeDisplayMode(true);
    }

    /**
     * Enable expanded mode
     *
     * @access public
     */
    public function expand()
    {
        $this->changeDisplayMode(false);
    }

    /**
     * Change display mode
     *
     * @access private
     */
    private function changeDisplayMode($mode)
    {
        $project_id = $this->request->getIntegerParam('project_id');
        $this->userSession->setBoardDisplayMode($project_id, $mode);

        if ($this->request->isAjax()) {
            $this->response->html($this->renderBoard($project_id));
        } else {
            $this->response->redirect($this->helper->url->to('board', 'show', array('project_id' => $project_id)));
        }
    }

    /**
     * Render board
     *
     * @access private
     */
    private function renderBoard($project_id)
    {
        return $this->template->render('board/table_container', array(
            'project' => $this->project->getById($project_id),
            'swimlanes' => $this->taskFilter->search($this->userSession->getFilters($project_id))->getBoard($project_id),
            'board_private_refresh_interval' => $this->config->get('board_private_refresh_interval'),
            'board_highlight_period' => $this->config->get('board_highlight_period'),
        ));
    }
}
