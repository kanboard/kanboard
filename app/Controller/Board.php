<?php

namespace Controller;

use Model\Project as ProjectModel;
use Model\User as UserModel;
use Core\Security;

/**
 * Board controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class Board extends Base
{
    /**
     * Move a column up
     *
     * @access public
     */
    public function moveUp()
    {
        $this->checkCSRFParam();
        $project_id = $this->request->getIntegerParam('project_id');
        $column_id = $this->request->getIntegerParam('column_id');

        $this->board->moveUp($project_id, $column_id);

        $this->response->redirect('?controller=board&action=edit&project_id='.$project_id);
    }

    /**
     * Move a column down
     *
     * @access public
     */
    public function moveDown()
    {
        $this->checkCSRFParam();
        $project_id = $this->request->getIntegerParam('project_id');
        $column_id = $this->request->getIntegerParam('column_id');

        $this->board->moveDown($project_id, $column_id);

        $this->response->redirect('?controller=board&action=edit&project_id='.$project_id);
    }

    /**
     * Change a task assignee directly from the board
     *
     * @access public
     */
    public function assign()
    {
        $task = $this->task->getById($this->request->getIntegerParam('task_id'));
        $project = $this->project->getById($task['project_id']);
        $projects = $this->project->getListByStatus(ProjectModel::ACTIVE);

        if ($this->acl->isRegularUser()) {
            $projects = $this->project->filterListByAccess($projects, $this->acl->getUserId());
        }

        if (! $project) $this->notfound();
        $this->checkProjectPermissions($project['id']);

        if ($this->request->isAjax()) {

            $this->response->html($this->template->load('board_assign', array(
                'errors' => array(),
                'values' => $task,
                'users_list' => $this->project->getUsersList($project['id']),
                'projects' => $projects,
                'current_project_id' => $project['id'],
                'current_project_name' => $project['name'],
            )));
        }
        else {

            $this->response->html($this->template->layout('board_assign', array(
                'errors' => array(),
                'values' => $task,
                'users_list' => $this->project->getUsersList($project['id']),
                'projects' => $projects,
                'current_project_id' => $project['id'],
                'current_project_name' => $project['name'],
                'menu' => 'boards',
                'title' => t('Change assignee').' - '.$task['title'],
            )));
        }
    }

    /**
     * Validate an assignee modification
     *
     * @access public
     */
    public function assignTask()
    {
        $values = $this->request->getValues();
        $this->checkProjectPermissions($values['project_id']);

        list($valid,) = $this->task->validateAssigneeModification($values);

        if ($valid && $this->task->update($values)) {
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
            $this->response->text('Not Authorized', 401);
        }

        // Display the board with a specific layout
        $this->response->html($this->template->layout('board_public', array(
            'project' => $project,
            'columns' => $this->board->get($project['id']),
            'categories' => $this->category->getList($project['id'], false),
            'title' => $project['name'],
            'no_layout' => true,
            'auto_refresh' => true,
        )));
    }

    /**
     * Redirect the user to the default project
     *
     * @access public
     */
    public function index()
    {
        $projects = $this->project->getListByStatus(ProjectModel::ACTIVE);
        $project_id = 0;
        $project_name = '';

        if ($this->acl->isRegularUser()) {
            $projects = $this->project->filterListByAccess($projects, $this->acl->getUserId());
        }

        if (empty($projects)) {

            if ($this->acl->isAdminUser()) {
                $this->redirectNoProject();
            }
            else {
                $this->response->redirect('?controller=project&action=forbidden');
            }
        }
        else if (! empty($_SESSION['user']['default_project_id']) && isset($projects[$_SESSION['user']['default_project_id']])) {
            $project_id = $_SESSION['user']['default_project_id'];
            $project_name = $projects[$_SESSION['user']['default_project_id']];
        }
        else {
            list($project_id, $project_name) = each($projects);
        }

        $this->response->redirect('?controller=board&action=show&project_id='.$project_id);
    }

    /**
     * Show a board for a given project
     *
     * @access public
     */
    public function show()
    {
        $project_id = $this->request->getIntegerParam('project_id');
        $user_id = $this->request->getIntegerParam('user_id', UserModel::EVERYBODY_ID);

        $this->checkProjectPermissions($project_id);
        $projects = $this->project->getAvailableList($this->acl->getUserId());

        if (! isset($projects[$project_id])) {
            $this->notfound();
        }

        $this->response->html($this->template->layout('board_index', array(
            'users' => $this->project->getUsersList($project_id, true, true),
            'filters' => array('user_id' => $user_id),
            'projects' => $projects,
            'current_project_id' => $project_id,
            'current_project_name' => $projects[$project_id],
            'board' => $this->board->get($project_id),
            'categories' => $this->category->getList($project_id, true, true),
            'menu' => 'boards',
            'title' => $projects[$project_id],
            'board_selector' => $projects,
        )));
    }

    /**
     * Display a form to edit a board
     *
     * @access public
     */
    public function edit()
    {
        $project_id = $this->request->getIntegerParam('project_id');
        $project = $this->project->getById($project_id);

        if (! $project) $this->notfound();

        $columns = $this->board->getColumns($project_id);
        $values = array();

        foreach ($columns as $column) {
            $values['title['.$column['id'].']'] = $column['title'];
            $values['task_limit['.$column['id'].']'] = $column['task_limit'] ?: null;
        }

        $this->response->html($this->template->layout('board_edit', array(
            'errors' => array(),
            'values' => $values + array('project_id' => $project_id),
            'columns' => $columns,
            'project' => $project,
            'menu' => 'projects',
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
        $project_id = $this->request->getIntegerParam('project_id');
        $project = $this->project->getById($project_id);

        if (! $project) $this->notfound();

        $columns = $this->board->getColumns($project_id);
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

        $this->response->html($this->template->layout('board_edit', array(
            'errors' => $errors,
            'values' => $values + array('project_id' => $project_id),
            'columns' => $columns,
            'project' => $project,
            'menu' => 'projects',
            'title' => t('Edit board')
        )));
    }

    /**
     * Validate and add a new column
     *
     * @access public
     */
    public function add()
    {
        $project_id = $this->request->getIntegerParam('project_id');
        $project = $this->project->getById($project_id);

        if (! $project) $this->notfound();

        $columns = $this->board->getColumnsList($project_id);
        $data = $this->request->getValues();
        $values = array();

        foreach ($columns as $column_id => $column_title) {
            $values['title['.$column_id.']'] = $column_title;
        }

        list($valid, $errors) = $this->board->validateCreation($data);

        if ($valid) {

            if ($this->board->add($data)) {
                $this->session->flash(t('Board updated successfully.'));
                $this->response->redirect('?controller=board&action=edit&project_id='.$project['id']);
            }
            else {
                $this->session->flashError(t('Unable to update this board.'));
            }
        }

        $this->response->html($this->template->layout('board_edit', array(
            'errors' => $errors,
            'values' => $values + $data,
            'columns' => $columns,
            'project' => $project,
            'menu' => 'projects',
            'title' => t('Edit board')
        )));
    }

    /**
     * Confirmation dialog before removing a column
     *
     * @access public
     */
    public function confirm()
    {
        $this->response->html($this->template->layout('board_remove', array(
            'column' => $this->board->getColumn($this->request->getIntegerParam('column_id')),
            'menu' => 'projects',
            'title' => t('Remove a column from a board')
        )));
    }

    /**
     * Remove a column
     *
     * @access public
     */
    public function remove()
    {
        $this->checkCSRFParam();
        $column = $this->board->getColumn($this->request->getIntegerParam('column_id'));

        if ($column && $this->board->removeColumn($column['id'])) {
            $this->session->flash(t('Column removed successfully.'));
        } else {
            $this->session->flashError(t('Unable to remove this column.'));
        }

        $this->response->redirect('?controller=board&action=edit&project_id='.$column['project_id']);
    }

    /**
     * Save the board (Ajax request made by the drag and drop)
     *
     * @access public
     */
    public function save()
    {
        if ($this->request->isAjax()) {

            $project_id = $this->request->getIntegerParam('project_id');
            $values = $this->request->getValues();

            if ($project_id > 0 && ! $this->project->isUserAllowed($project_id, $this->acl->getUserId())) {
                $this->response->text('Not Authorized', 401);
            }

            if (isset($values['positions'])) {
                $this->board->saveTasksPosition($values['positions']);
            }

            $this->response->html(
                $this->template->load('board_show', array(
                    'current_project_id' => $project_id,
                    'board' => $this->board->get($project_id),
                    'categories' => $this->category->getList($project_id, false),
                )),
                201
            );
        }
        else {
            $this->response->status(401);
        }
    }

    /**
     * Check if the board have been changed
     *
     * @access public
     */
    public function check()
    {
        if ($this->request->isAjax()) {

            $project_id = $this->request->getIntegerParam('project_id');
            $timestamp = $this->request->getIntegerParam('timestamp');

            if ($project_id > 0 && ! $this->project->isUserAllowed($project_id, $this->acl->getUserId())) {
                $this->response->text('Not Authorized', 401);
            }

            if ($this->project->isModifiedSince($project_id, $timestamp)) {
                $this->response->html(
                    $this->template->load('board_show', array(
                        'current_project_id' => $project_id,
                        'board' => $this->board->get($project_id),
                        'categories' => $this->category->getList($project_id, false),
                    ))
                );
            }
            else {
                $this->response->status(304);
            }
        }
        else {
            $this->response->status(401);
        }
    }
}
