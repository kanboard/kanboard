<?php

namespace Controller;

/**
 * Board controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class Board extends Base
{
    /**
     * Move a column down or up
     *
     * @access public
     */
    public function moveColumn()
    {
        $this->checkCSRFParam();
        $project = $this->getProject();
        $column_id = $this->request->getIntegerParam('column_id');
        $direction = $this->request->getStringParam('direction');

        if ($direction === 'up' || $direction === 'down') {
            $this->board->{'move'.$direction}($project['id'], $column_id);
        }

        $this->response->redirect('?controller=board&action=edit&project_id='.$project['id']);
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

        $this->response->html($this->template->render('board/assignee', array(
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

        list($valid,) = $this->taskValidator->validateAssigneeModification($values);

        if ($valid && $this->taskModification->update($values)) {
            $this->session->flash(t('Task updated successfully.'));
        }
        else {
            $this->session->flashError(t('Unable to update your task.'));
        }

        $this->response->redirect('?controller=board&action=show&project_id='.$values['project_id']);
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

        $this->response->html($this->template->render('board/category', array(
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

        list($valid,) = $this->taskValidator->validateCategoryModification($values);

        if ($valid && $this->taskModification->update($values)) {
            $this->session->flash(t('Task updated successfully.'));
        }
        else {
            $this->session->flashError(t('Unable to update your task.'));
        }

        $this->response->redirect('?controller=board&action=show&project_id='.$values['project_id']);
    }

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
        if (! $project) {
            $this->forbidden(true);
        }

        // Display the board with a specific layout
        $this->response->html($this->template->layout('board/public', array(
            'project' => $project,
            'swimlanes' => $this->board->getBoard($project['id']),
            'categories' => $this->category->getList($project['id'], false),
            'title' => $project['name'],
            'no_layout' => true,
            'not_editable' => true,
            'board_public_refresh_interval' => $this->config->get('board_public_refresh_interval'),
            'board_private_refresh_interval' => $this->config->get('board_private_refresh_interval'),
            'board_highlight_period' => $this->config->get('board_highlight_period'),
        )));
    }

    /**
     * Redirect the user to the default project
     *
     * @access public
     */
    public function index()
    {
        $last_seen_project_id = $this->userSession->getLastSeenProjectId();
        $favorite_project_id = $this->userSession->getFavoriteProjectId();
        $project_id = $last_seen_project_id ?: $favorite_project_id;

        if (! $project_id) {
            $projects = $this->projectPermission->getAllowedProjects($this->userSession->getId());

            if (empty($projects)) {

                if ($this->userSession->isAdmin()) {
                    $this->redirectNoProject();
                }

                $this->forbidden();
            }

            $project_id = key($projects);
        }

        $this->show($project_id);
    }

    /**
     * Show a board for a given project
     *
     * @access public
     * @param  integer   $project_id    Default project id
     */
    public function show($project_id = 0)
    {
        $project = $this->getProject($project_id);
        $projects = $this->projectPermission->getAllowedProjects($this->userSession->getId());

        $board_selector = $projects;
        unset($board_selector[$project['id']]);

        $this->userSession->storeLastSeenProjectId($project['id']);

        $this->response->html($this->template->layout('board/index', array(
            'users' => $this->projectPermission->getMemberList($project['id'], true, true),
            'projects' => $projects,
            'project' => $project,
            'swimlanes' => $this->board->getBoard($project['id']),
            'categories' => $this->category->getList($project['id'], true, true),
            'title' => $project['name'],
            'board_selector' => $board_selector,
            'board_private_refresh_interval' => $this->config->get('board_private_refresh_interval'),
            'board_highlight_period' => $this->config->get('board_highlight_period'),
        )));
    }

    /**
     * Display a form to edit a board
     *
     * @access public
     */
    public function edit(array $values = array(), array $errors = array())
    {
        $project = $this->getProject();
        $columns = $this->board->getColumns($project['id']);

        foreach ($columns as $column) {
            $values['title['.$column['id'].']'] = $column['title'];
            $values['task_limit['.$column['id'].']'] = $column['task_limit'] ?: null;
        }

        $this->response->html($this->projectLayout('board/edit', array(
            'errors' => $errors,
            'values' => $values + array('project_id' => $project['id']),
            'columns' => $columns,
            'project' => $project,
            'title' => t('Edit board')
        )));
    }

    /**
     * Validate and update a board
     *
     * @access public
     */
    public function update()
    {
        $project = $this->getProject();
        $columns = $this->board->getColumns($project['id']);
        $data = $this->request->getValues();
        $values = $columns_list = array();

        foreach ($columns as $column) {
            $columns_list[$column['id']] = $column['title'];
            $values['title['.$column['id'].']'] = isset($data['title'][$column['id']]) ? $data['title'][$column['id']] : '';
            $values['task_limit['.$column['id'].']'] = isset($data['task_limit'][$column['id']]) ? $data['task_limit'][$column['id']] : 0;
        }

        list($valid, $errors) = $this->board->validateModification($columns_list, $values);

        if ($valid) {

            if ($this->board->update($data)) {
                $this->session->flash(t('Board updated successfully.'));
                $this->response->redirect('?controller=board&action=edit&project_id='.$project['id']);
            }
            else {
                $this->session->flashError(t('Unable to update this board.'));
            }
        }

        $this->edit($values, $errors);
    }

    /**
     * Validate and add a new column
     *
     * @access public
     */
    public function add()
    {
        $project = $this->getProject();
        $columns = $this->board->getColumnsList($project['id']);
        $data = $this->request->getValues();
        $values = array();

        foreach ($columns as $column_id => $column_title) {
            $values['title['.$column_id.']'] = $column_title;
        }

        list($valid, $errors) = $this->board->validateCreation($data);

        if ($valid) {

            if ($this->board->addColumn($project['id'], $data['title'])) {
                $this->session->flash(t('Board updated successfully.'));
                $this->response->redirect('?controller=board&action=edit&project_id='.$project['id']);
            }
            else {
                $this->session->flashError(t('Unable to update this board.'));
            }
        }

        $this->edit($values, $errors);
    }

    /**
     * Remove a column
     *
     * @access public
     */
    public function remove()
    {
        $project = $this->getProject();

        if ($this->request->getStringParam('remove') === 'yes') {

            $this->checkCSRFParam();
            $column = $this->board->getColumn($this->request->getIntegerParam('column_id'));

            if ($column && $this->board->removeColumn($column['id'])) {
                $this->session->flash(t('Column removed successfully.'));
            } else {
                $this->session->flashError(t('Unable to remove this column.'));
            }

            $this->response->redirect('?controller=board&action=edit&project_id='.$project['id']);
        }

        $this->response->html($this->projectLayout('board/remove', array(
            'column' => $this->board->getColumn($this->request->getIntegerParam('column_id')),
            'project' => $project,
            'title' => t('Remove a column from a board')
        )));
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

        $this->response->html(
            $this->template->render('board/show', array(
                'project' => $this->project->getById($project_id),
                'swimlanes' => $this->board->getBoard($project_id),
                'categories' => $this->category->getList($project_id, false),
                'board_private_refresh_interval' => $this->config->get('board_private_refresh_interval'),
                'board_highlight_period' => $this->config->get('board_highlight_period'),
            )),
            201
        );
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

        $this->response->html(
            $this->template->render('board/show', array(
                'project' => $this->project->getById($project_id),
                'swimlanes' => $this->board->getBoard($project_id),
                'categories' => $this->category->getList($project_id, false),
                'board_private_refresh_interval' => $this->config->get('board_private_refresh_interval'),
                'board_highlight_period' => $this->config->get('board_highlight_period'),
            ))
        );
    }

    /**
     * Get subtasks on mouseover
     *
     * @access public
     */
    public function subtasks()
    {
        $task = $this->getTask();
        $this->response->html($this->template->render('board/subtasks', array(
            'subtasks' => $this->subTask->getAll($task['id']),
            'task' => $task,
        )));
    }

    /**
     * Change the status of a subtask from the mouseover
     *
     * @access public
     */
    public function toggleSubtask()
    {
        $task = $this->getTask();
        $this->subTask->toggleStatus($this->request->getIntegerParam('subtask_id'));

        $this->response->html($this->template->render('board/subtasks', array(
            'subtasks' => $this->subTask->getAll($task['id']),
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

        $this->response->html($this->template->render('board/files', array(
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

        $this->response->html($this->template->render('board/comments', array(
            'comments' => $this->comment->getAll($task['id'])
        )));
    }

    /**
     * Display the description
     *
     * @access public
     */
    public function description()
    {
        $task = $this->getTask();

        $this->response->html($this->template->render('board/description', array(
            'task' => $task
        )));
    }
}
