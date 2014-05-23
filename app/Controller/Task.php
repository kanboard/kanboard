<?php

namespace Controller;

use Model\Project;
use Model\File;

/**
 * Task controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class Task extends Base
{
    private function getTask()
    {
        $task = $this->task->getById($this->request->getIntegerParam('task_id'), true);

        if (! $task) {
            $this->notfound();
        }

        $this->checkProjectPermissions($task['project_id']);

        return $task;
    }

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
        $this->showTask($this->getTask());
    }

    /**
     * Add a description from the show task page
     *
     * @access public
     */
    public function description()
    {
        $task = $this->task->getById($this->request->getIntegerParam('task_id'), true);
        $values = $this->request->getValues();

        if (! $task) $this->notfound();
        $this->checkProjectPermissions($task['project_id']);

        list($valid, $errors) = $this->task->validateDescriptionCreation($values);

        if ($valid) {

            if ($this->task->update($values)) {
                $this->session->flash(t('Task updated successfully.'));
            }
            else {
                $this->session->flashError(t('Unable to update your task.'));
            }

            $this->response->redirect('?controller=task&action=show&task_id='.$task['id']);
        }

        $this->showTask(
            $task,
            array(),
            array('values' => $values, 'errors' => $errors)
        );
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
            'projects_list' => $this->project->getListByStatus(\Model\Project::ACTIVE),
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
            'projects_list' => $this->project->getListByStatus(Project::ACTIVE),
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
        $task = $this->task->getById($this->request->getIntegerParam('task_id'));

        if (! $task) $this->notfound();
        $this->checkProjectPermissions($task['project_id']);

        if (! empty($task['date_due'])) {
            $task['date_due'] = date(t('m/d/Y'), $task['date_due']);
        }
        else {
            $task['date_due'] = '';
        }

        $task['score'] = $task['score'] ?: '';

        $this->response->html($this->template->layout('task_edit', array(
            'errors' => array(),
            'values' => $task,
            'columns_list' => $this->board->getColumnsList($task['project_id']),
            'users_list' => $this->project->getUsersList($task['project_id']),
            'colors_list' => $this->task->getColors(),
            'categories_list' => $this->category->getList($task['project_id']),
            'menu' => 'tasks',
            'title' => t('Edit a task')
        )));
    }

    /**
     * Validate and update a task
     *
     * @access public
     */
    public function update()
    {
        $values = $this->request->getValues();
        $this->checkProjectPermissions($values['project_id']);

        list($valid, $errors) = $this->task->validateModification($values);

        if ($valid) {

            if ($this->task->update($values)) {
                $this->session->flash(t('Task updated successfully.'));
                $this->response->redirect('?controller=task&action=show&task_id='.$values['id']);
            }
            else {
                $this->session->flashError(t('Unable to update your task.'));
            }
        }

        $this->response->html($this->template->layout('task_edit', array(
            'errors' => $errors,
            'values' => $values,
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
            'projects_list' => $this->project->getListByStatus(Project::ACTIVE),
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
     * File upload form
     *
     * @access public
     */
    public function file()
    {
        $task = $this->getTask();

        $this->response->html($this->taskLayout('task_upload', array(
            'task' => $task,
            'menu' => 'tasks',
            'title' => t('Attach a document')
        )));
    }

    /**
     * File upload (save files)
     *
     * @access public
     */
    public function upload()
    {
        $task = $this->getTask();
        $this->file->upload($task['project_id'], $task['id'], 'files');
        $this->response->redirect('?controller=task&action=show&task_id='.$task['id'].'#attachments');
    }

    /**
     * File download
     *
     * @access public
     */
    public function download()
    {
        $task = $this->getTask();
        $file = $this->file->getById($this->request->getIntegerParam('file_id'));
        $filename = File::BASE_PATH.$file['path'];

        if ($file['task_id'] == $task['id'] && file_exists($filename)) {
            $this->response->forceDownload($file['name']);
            $this->response->binary(file_get_contents($filename));
        }

        $this->response->redirect('?controller=task&action=show&task_id='.$task['id']);
    }

    /**
     * Open a file (show the content in a popover)
     *
     * @access public
     */
    public function openFile()
    {
        $task = $this->getTask();
        $file = $this->file->getById($this->request->getIntegerParam('file_id'));

        if ($file['task_id'] == $task['id']) {
            $this->response->html($this->template->load('task_open_file', array(
                'file' => $file
            )));
        }
    }

    /**
     * Return the file content (work only for images)
     *
     * @access public
     */
    public function image()
    {
        $task = $this->getTask();
        $file = $this->file->getById($this->request->getIntegerParam('file_id'));
        $filename = File::BASE_PATH.$file['path'];

        if ($file['task_id'] == $task['id'] && file_exists($filename)) {
            $metadata = getimagesize($filename);

            if (isset($metadata['mime'])) {
                $this->response->contentType($metadata['mime']);
                readfile($filename);
            }
        }
    }

    /**
     * Remove a file
     *
     * @access public
     */
    public function removeFile()
    {
        $task = $this->getTask();
        $file = $this->file->getById($this->request->getIntegerParam('file_id'));

        if ($file['task_id'] == $task['id'] && $this->file->remove($file['id'])) {
            $this->session->flash(t('File removed successfully.'));
        } else {
            $this->session->flashError(t('Unable to remove this file.'));
        }

        $this->response->redirect('?controller=task&action=show&task_id='.$task['id']);
    }

    /**
     * Confirmation dialog before removing a file
     *
     * @access public
     */
    public function confirmRemoveFile()
    {
        $task = $this->getTask();
        $file = $this->file->getById($this->request->getIntegerParam('file_id'));

        $this->response->html($this->taskLayout('task_remove_file', array(
            'task' => $task,
            'file' => $file,
            'menu' => 'tasks',
            'title' => t('Remove a file')
        )));
    }
}
