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
     * Webhook to create a task (useful for external software)
     *
     * @access public
     */
    public function add()
    {
        $token = $this->request->getStringParam('token');

        if ($this->config->get('webhooks_token') !== $token) {
            $this->response->text('Not Authorized', 401);
        }

        $defaultProject = $this->project->getFirst();

        $values = array(
            'title' => $this->request->getStringParam('title'),
            'description' => $this->request->getStringParam('description'),
            'color_id' => $this->request->getStringParam('color_id', 'blue'),
            'project_id' => $this->request->getIntegerParam('project_id', $defaultProject['id']),
            'owner_id' => $this->request->getIntegerParam('owner_id'),
            'column_id' => $this->request->getIntegerParam('column_id'),
            'category_id' => $this->request->getIntegerParam('category_id'),
        );

        if ($values['column_id'] == 0) {
            $values['column_id'] = $this->board->getFirstColumn($values['project_id']);
        }

        list($valid,) = $this->task->validateCreation($values);

        if ($valid && $this->task->create($values)) {
            $this->response->text('OK');
        }

        $this->response->text('FAILED');
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
            'files' => $this->file->getAll($task['id']),
            'comments' => $this->comment->getAll($task['id']),
            'subtasks' => $this->subTask->getAll($task['id']),
            'task' => $task,
            'columns_list' => $this->board->getColumnsList($task['project_id']),
            'colors_list' => $this->task->getColors(),
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
            'users_list' => $this->project->getUsersList($project_id),
            'colors_list' => $this->task->getColors(),
            'categories_list' => $this->category->getList($project_id),
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

        list($valid, $errors) = $this->task->validateCreation($values);

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
            'users_list' => $this->project->getUsersList($values['project_id']),
            'colors_list' => $this->task->getColors(),
            'categories_list' => $this->category->getList($values['project_id']),
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
            $task['date_due'] = date(t('m/d/Y'), $task['date_due']);
        }
        else {
            $task['date_due'] = '';
        }

        $task['score'] = $task['score'] ?: '';

        $params = array(
                'values' => $task,
                'errors' => array(),
                'task' => $task,
                'columns_list' => $this->board->getColumnsList($task['project_id']),
                'users_list' => $this->project->getUsersList($task['project_id']),
                'colors_list' => $this->task->getColors(),
                'categories_list' => $this->category->getList($task['project_id']),
                'ajax' => $this->request->isAjax(),
                'menu' => 'tasks',
                'title' => t('Edit a task')
            );
        if ($this->request->isAjax()) {
            $this->response->html($this->template->load('task_edit', $params));
        }
        else {
            $this->response->html($this->template->layout('task_edit', $params));
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

        list($valid, $errors) = $this->task->validateModification($values);

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

        $this->response->html($this->template->layout('task_edit', array(
            'values' => $values,
            'errors' => $errors,
            'task' => $task,
            'columns_list' => $this->board->getColumnsList($values['project_id']),
            'users_list' => $this->project->getUsersList($values['project_id']),
            'colors_list' => $this->task->getColors(),
            'categories_list' => $this->category->getList($values['project_id']),
            'menu' => 'tasks',
            'title' => t('Edit a task')
        )));
    }

    /**
     * Hide a task
     *
     * @access public
     */
    public function close()
    {
        $this->checkCSRFParam();
        $task = $this->getTask();

        if ($this->task->close($task['id'])) {
            $this->session->flash(t('Task closed successfully.'));
        } else {
            $this->session->flashError(t('Unable to close this task.'));
        }

        $this->response->redirect('?controller=task&action=show&task_id='.$task['id']);
    }

    /**
     * Confirmation dialog before to close a task
     *
     * @access public
     */
    public function confirmClose()
    {
        $task = $this->getTask();

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
        $this->checkCSRFParam();
        $task = $this->getTask();

        if ($this->task->open($task['id'])) {
            $this->session->flash(t('Task opened successfully.'));
        } else {
            $this->session->flashError(t('Unable to open this task.'));
        }

        $this->response->redirect('?controller=task&action=show&task_id='.$task['id']);
    }

    /**
     * Confirmation dialog before to open a task
     *
     * @access public
     */
    public function confirmOpen()
    {
        $task = $this->getTask();

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
        $this->checkCSRFParam();
        $task = $this->getTask();

        if ($this->task->remove($task['id'])) {
            $this->session->flash(t('Task removed successfully.'));
        } else {
            $this->session->flashError(t('Unable to remove this task.'));
        }

        $this->response->redirect('?controller=board&action=show&project_id='.$task['project_id']);
    }

    /**
     * Confirmation dialog before removing a task
     *
     * @access public
     */
    public function confirmRemove()
    {
        $task = $this->getTask();

        $this->response->html($this->taskLayout('task_remove', array(
            'task' => $task,
            'menu' => 'tasks',
            'title' => t('Remove a task')
        )));
    }

    /**
     * Duplicate a task (fill the form for a new task)
     *
     * @access public
     */
    public function duplicate()
    {
        $task = $this->getTask();

        if (! empty($task['date_due'])) {
            $task['date_due'] = date(t('m/d/Y'), $task['date_due']);
        }
        else {
            $task['date_due'] = '';
        }

        $task['score'] = $task['score'] ?: '';

        $this->response->html($this->template->layout('task_new', array(
            'errors' => array(),
            'values' => $task,
            'projects_list' => $this->project->getListByStatus(ProjectModel::ACTIVE),
            'columns_list' => $this->board->getColumnsList($task['project_id']),
            'users_list' => $this->project->getUsersList($task['project_id']),
            'colors_list' => $this->task->getColors(),
            'categories_list' => $this->category->getList($task['project_id']),
            'duplicate' => true,
            'menu' => 'tasks',
            'title' => t('New task')
        )));
    }

    /**
     * Edit description form
     *
     * @access public
     */
    public function editDescription()
    {
        $task = $this->getTask();

        $params = array(
                'values' => $task,
                'errors' => array(),
                'task' => $task,
                'ajax' => $this->request->isAjax(),
                'menu' => 'tasks',
                'title' => t('Edit the description')
            );
        if ($this->request->isAjax()) {
            $this->response->html($this->template->load('task_edit_description', $params));
        }
        else {
            $this->response->html($this->taskLayout('task_edit_description', $params));
        }
    }

    /**
     * Save and validation the description
     *
     * @access public
     */
    public function saveDescription()
    {
        $task = $this->getTask();
        $values = $this->request->getValues();

        list($valid, $errors) = $this->task->validateDescriptionCreation($values);

        if ($valid) {

            if ($this->task->update($values)) {
                $this->session->flash(t('Task updated successfully.'));
            }
            else {
                $this->session->flashError(t('Unable to update your task.'));
            }

            if ($this->request->getIntegerParam('ajax')) {
                $this->response->redirect('?controller=board&action=show&project_id='.$task['project_id']);
            }
            else {
                $this->response->redirect('?controller=task&action=show&task_id='.$task['id']);
            }
        }

        $this->response->html($this->taskLayout('task_edit_description', array(
            'values' => $values,
            'errors' => $errors,
            'task' => $task,
            'menu' => 'tasks',
            'title' => t('Edit the description')
        )));
    }
}
