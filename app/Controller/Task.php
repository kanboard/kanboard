<?php

namespace Controller;

use Model\Project as ProjectModel;

/**
 * Task controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class Task extends Base
{
    /**
     * Public access (display a task)
     *
     * @access public
     */
    public function readonly()
    {
        $project = $this->project->getByToken($this->request->getStringParam('token'));

        // Token verification
        if (! $project) {
            $this->forbidden(true);
        }

        $task = $this->task->getDetails($this->request->getIntegerParam('task_id'));

        if (! $task) {
            $this->notfound(true);
        }

        $this->response->html($this->template->layout('task_public', array(
            'project' => $project,
            'comments' => $this->comment->getAll($task['id']),
            'subtasks' => $this->subTask->getAll($task['id']),
            'task' => $task,
            'columns_list' => $this->board->getColumnsList($task['project_id']),
            'colors_list' => $this->color->getList(),
            'title' => $task['title'],
            'no_layout' => true,
            'auto_refresh' => true,
            'not_editable' => true,
        )));
    }

    /**
     * Show a task
     *
     * @access public
     */
    public function show()
    {
        $task = $this->getTask();

        $this->response->html($this->taskLayout('task_show', array(
            'project' => $this->project->getById($task['project_id']),
            'files' => $this->file->getAll($task['id']),
            'comments' => $this->comment->getAll($task['id']),
            'subtasks' => $this->subTask->getAll($task['id']),
            'task' => $task,
            'columns_list' => $this->board->getColumnsList($task['project_id']),
            'colors_list' => $this->color->getList(),
            'menu' => 'tasks',
            'title' => $task['title'],
        )));
    }

    /**
     * Display a form to create a new task
     *
     * @access public
     */
    public function create()
    {
        $project_id = $this->request->getIntegerParam('project_id');
        $this->checkProjectPermissions($project_id);

        $this->response->html($this->template->layout('task_new', array(
            'errors' => array(),
            'values' => array(
                'project_id' => $project_id,
                'column_id' => $this->request->getIntegerParam('column_id'),
                'color_id' => $this->request->getStringParam('color_id'),
                'owner_id' => $this->request->getIntegerParam('owner_id'),
                'another_task' => $this->request->getIntegerParam('another_task'),
            ),
            'projects_list' => $this->project->getListByStatus(ProjectModel::ACTIVE),
            'columns_list' => $this->board->getColumnsList($project_id),
            'users_list' => $this->projectPermission->getUsersList($project_id),
            'colors_list' => $this->color->getList(),
            'categories_list' => $this->category->getList($project_id),
            'date_format' => $this->config->get('application_date_format'),
            'date_formats' => $this->dateParser->getAvailableFormats(),
            'menu' => 'tasks',
            'title' => t('New task')
        )));
    }

    /**
     * Validate and save a new task
     *
     * @access public
     */
    public function save()
    {
        $values = $this->request->getValues();
        $values['creator_id'] = $this->acl->getUserId();

        $this->checkProjectPermissions($values['project_id']);

        list($valid, $errors) = $this->taskValidator->validateCreation($values);

        if ($valid) {

            if ($this->task->create($values)) {
                $this->session->flash(t('Task created successfully.'));

                if (isset($values['another_task']) && $values['another_task'] == 1) {
                    unset($values['title']);
                    unset($values['description']);
                    $this->response->redirect('?controller=task&action=create&'.http_build_query($values));
                }
                else {
                    $this->response->redirect('?controller=board&action=show&project_id='.$values['project_id']);
                }
            }
            else {
                $this->session->flashError(t('Unable to create your task.'));
            }
        }

        $this->response->html($this->template->layout('task_new', array(
            'errors' => $errors,
            'values' => $values,
            'projects_list' => $this->project->getListByStatus(ProjectModel::ACTIVE),
            'columns_list' => $this->board->getColumnsList($values['project_id']),
            'users_list' => $this->projectPermission->getUsersList($values['project_id']),
            'colors_list' => $this->color->getList(),
            'categories_list' => $this->category->getList($values['project_id']),
            'date_format' => $this->config->get('application_date_format'),
            'date_formats' => $this->dateParser->getAvailableFormats(),
            'menu' => 'tasks',
            'title' => t('New task')
        )));
    }

    /**
     * Display a form to edit a task
     *
     * @access public
     */
    public function edit()
    {
        $task = $this->getTask();

        if (! empty($task['date_due'])) {
            $task['date_due'] = date($this->config->get('application_date_format'), $task['date_due']);
        }
        else {
            $task['date_due'] = '';
        }

        $task['score'] = $task['score'] ?: '';
        $ajax = $this->request->isAjax();

        $params = array(
            'values' => $task,
            'errors' => array(),
            'task' => $task,
            'users_list' => $this->projectPermission->getUsersList($task['project_id']),
            'colors_list' => $this->color->getList(),
            'categories_list' => $this->category->getList($task['project_id']),
            'date_format' => $this->config->get('application_date_format'),
            'date_formats' => $this->dateParser->getAvailableFormats(),
            'ajax' => $ajax,
            'menu' => 'tasks',
            'title' => t('Edit a task')
        );

        if ($ajax) {
            $this->response->html($this->template->load('task_edit', $params));
        }
        else {
            $this->response->html($this->taskLayout('task_edit', $params));
        }
    }

    /**
     * Validate and update a task
     *
     * @access public
     */
    public function update()
    {
        $task = $this->getTask();
        $values = $this->request->getValues();

        list($valid, $errors) = $this->taskValidator->validateModification($values);

        if ($valid) {

            if ($this->task->update($values)) {
                $this->session->flash(t('Task updated successfully.'));

                if ($this->request->getIntegerParam('ajax')) {
                    $this->response->redirect('?controller=board&action=show&project_id='.$task['project_id']);
                }
                else {
                    $this->response->redirect('?controller=task&action=show&task_id='.$values['id']);
                }
            }
            else {
                $this->session->flashError(t('Unable to update your task.'));
            }
        }

        $this->response->html($this->taskLayout('task_edit', array(
            'values' => $values,
            'errors' => $errors,
            'task' => $task,
            'columns_list' => $this->board->getColumnsList($values['project_id']),
            'users_list' => $this->projectPermission->getUsersList($values['project_id']),
            'colors_list' => $this->color->getList(),
            'categories_list' => $this->category->getList($values['project_id']),
            'date_format' => $this->config->get('application_date_format'),
            'date_formats' => $this->dateParser->getAvailableFormats(),
            'menu' => 'tasks',
            'title' => t('Edit a task'),
            'ajax' => $this->request->isAjax(),
        )));
    }

    /**
     * Hide a task
     *
     * @access public
     */
    public function close()
    {
        $task = $this->getTask();

        if ($this->request->getStringParam('confirmation') === 'yes') {

            $this->checkCSRFParam();

            if ($this->task->close($task['id'])) {
                $this->session->flash(t('Task closed successfully.'));
            } else {
                $this->session->flashError(t('Unable to close this task.'));
            }

            $this->response->redirect('?controller=task&action=show&task_id='.$task['id']);
        }

        $this->response->html($this->taskLayout('task_close', array(
            'task' => $task,
            'menu' => 'tasks',
            'title' => t('Close a task')
        )));
    }

    /**
     * Open a task
     *
     * @access public
     */
    public function open()
    {
        $task = $this->getTask();

        if ($this->request->getStringParam('confirmation') === 'yes') {

            $this->checkCSRFParam();

            if ($this->task->open($task['id'])) {
                $this->session->flash(t('Task opened successfully.'));
            } else {
                $this->session->flashError(t('Unable to open this task.'));
            }

            $this->response->redirect('?controller=task&action=show&task_id='.$task['id']);
        }

        $this->response->html($this->taskLayout('task_open', array(
            'task' => $task,
            'menu' => 'tasks',
            'title' => t('Open a task')
        )));
    }

    /**
     * Remove a task
     *
     * @access public
     */
    public function remove()
    {
        $task = $this->getTask();

        if (! $this->taskPermission->canRemoveTask($task)) {
            $this->forbidden();
        }

        if ($this->request->getStringParam('confirmation') === 'yes') {

            $this->checkCSRFParam();

            if ($this->task->remove($task['id'])) {
                $this->session->flash(t('Task removed successfully.'));
            } else {
                $this->session->flashError(t('Unable to remove this task.'));
            }

            $this->response->redirect('?controller=board&action=show&project_id='.$task['project_id']);
        }

        $this->response->html($this->taskLayout('task_remove', array(
            'task' => $task,
            'menu' => 'tasks',
            'title' => t('Remove a task')
        )));
    }

    /**
     * Duplicate a task
     *
     * @access public
     */
    public function duplicate()
    {
        $task = $this->getTask();

        if ($this->request->getStringParam('confirmation') === 'yes') {

            $this->checkCSRFParam();
            $task_id = $this->task->duplicateSameProject($task);

            if ($task_id) {
                $this->session->flash(t('Task created successfully.'));
                $this->response->redirect('?controller=task&action=show&task_id='.$task_id);
            } else {
                $this->session->flashError(t('Unable to create this task.'));
                $this->response->redirect('?controller=task&action=duplicate&task_id='.$task['id']);
            }
        }

        $this->response->html($this->taskLayout('task_duplicate', array(
            'task' => $task,
            'menu' => 'tasks',
            'title' => t('Duplicate a task')
        )));
    }

    /**
     * Edit description form
     *
     * @access public
     */
    public function description()
    {
        $task = $this->getTask();
        $ajax = $this->request->isAjax() || $this->request->getIntegerParam('ajax');

        if ($this->request->isPost()) {

            $values = $this->request->getValues();

            list($valid, $errors) = $this->taskValidator->validateDescriptionCreation($values);

            if ($valid) {

                if ($this->task->update($values)) {
                    $this->session->flash(t('Task updated successfully.'));
                }
                else {
                    $this->session->flashError(t('Unable to update your task.'));
                }

                if ($ajax) {
                    $this->response->redirect('?controller=board&action=show&project_id='.$task['project_id']);
                }
                else {
                    $this->response->redirect('?controller=task&action=show&task_id='.$task['id']);
                }
            }
        }
        else {
            $values = $task;
            $errors = array();
        }

        $params = array(
            'values' => $values,
            'errors' => $errors,
            'task' => $task,
            'ajax' => $ajax,
            'menu' => 'tasks',
            'title' => t('Edit the description'),
        );

        if ($ajax) {
            $this->response->html($this->template->load('task_edit_description', $params));
        }
        else {
            $this->response->html($this->taskLayout('task_edit_description', $params));
        }
    }

    /**
     * Move a task to another project
     *
     * @access public
     */
    public function move()
    {
        $this->toAnotherProject('move');
    }

    /**
     * Duplicate a task to another project
     *
     * @access public
     */
    public function copy()
    {
        $this->toAnotherProject('duplicate');
    }

    /**
     * Common methods between the actions "move" and "copy"
     *
     * @access private
     */
    private function toAnotherProject($action)
    {
        $task = $this->getTask();
        $values = $task;
        $errors = array();
        $projects_list = $this->projectPermission->getAllowedProjects($this->acl->getUserId());

        unset($projects_list[$task['project_id']]);

        if ($this->request->isPost()) {

            $values = $this->request->getValues();
            list($valid, $errors) = $this->taskValidator->validateProjectModification($values);

            if ($valid) {
                $task_id = $this->task->{$action.'ToAnotherProject'}($values['project_id'], $task);
                if ($task_id) {
                    $this->session->flash(t('Task created successfully.'));
                    $this->response->redirect('?controller=task&action=show&task_id='.$task_id);
                }
                else {
                    $this->session->flashError(t('Unable to create your task.'));
                }
            }
        }

        $this->response->html($this->taskLayout('task_'.$action.'_project', array(
            'values' => $values,
            'errors' => $errors,
            'task' => $task,
            'projects_list' => $projects_list,
            'menu' => 'tasks',
            'title' => t(ucfirst($action).' the task to another project')
        )));
    }
}
