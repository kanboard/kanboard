<?php

namespace Kanboard\Controller;

use Kanboard\Model\Subtask as SubtaskModel;

/**
 * Subtask controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class Subtask extends Base
{
    /**
     * Get the current subtask
     *
     * @access private
     * @return array
     */
    private function getSubtask()
    {
        $subtask = $this->subtask->getById($this->request->getIntegerParam('subtask_id'));

        if (empty($subtask)) {
            $this->notfound();
        }

        return $subtask;
    }

    /**
     * Creation form
     *
     * @access public
     */
    public function create(array $values = array(), array $errors = array())
    {
        $task = $this->getTask();

        if (empty($values)) {
            $values = array(
                'task_id' => $task['id'],
                'another_subtask' => $this->request->getIntegerParam('another_subtask', 0)
            );
        }

        $this->response->html($this->taskLayout('subtask/create', array(
            'values' => $values,
            'errors' => $errors,
            'users_list' => $this->projectPermission->getMemberList($task['project_id']),
            'task' => $task,
        )));
    }

    /**
     * Validation and creation
     *
     * @access public
     */
    public function save()
    {
        $task = $this->getTask();
        $values = $this->request->getValues();

        list($valid, $errors) = $this->subtask->validateCreation($values);

        if ($valid) {
            if ($this->subtask->create($values)) {
                $this->session->flash(t('Sub-task added successfully.'));
            } else {
                $this->session->flashError(t('Unable to create your sub-task.'));
            }

            if (isset($values['another_subtask']) && $values['another_subtask'] == 1) {
                $this->response->redirect($this->helper->url->to('subtask', 'create', array('project_id' => $task['project_id'], 'task_id' => $task['id'], 'another_subtask' => 1)));
            }

            $this->response->redirect($this->helper->url->to('task', 'show', array('project_id' => $task['project_id'], 'task_id' => $task['id']), 'subtasks'));
        }

        $this->create($values, $errors);
    }

    /**
     * Edit form
     *
     * @access public
     */
    public function edit(array $values = array(), array $errors = array())
    {
        $task = $this->getTask();
        $subtask = $this->getSubTask();

        $this->response->html($this->taskLayout('subtask/edit', array(
            'values' => empty($values) ? $subtask : $values,
            'errors' => $errors,
            'users_list' => $this->projectPermission->getMemberList($task['project_id']),
            'status_list' => $this->subtask->getStatusList(),
            'subtask' => $subtask,
            'task' => $task,
        )));
    }

    /**
     * Update and validate a subtask
     *
     * @access public
     */
    public function update()
    {
        $task = $this->getTask();
        $this->getSubtask();

        $values = $this->request->getValues();
        list($valid, $errors) = $this->subtask->validateModification($values);

        if ($valid) {
            if ($this->subtask->update($values)) {
                $this->session->flash(t('Sub-task updated successfully.'));
            } else {
                $this->session->flashError(t('Unable to update your sub-task.'));
            }

            $this->response->redirect($this->helper->url->to('task', 'show', array('project_id' => $task['project_id'], 'task_id' => $task['id']), 'subtasks'));
        }

        $this->edit($values, $errors);
    }

    /**
     * Confirmation dialog before removing a subtask
     *
     * @access public
     */
    public function confirm()
    {
        $task = $this->getTask();
        $subtask = $this->getSubtask();

        $this->response->html($this->taskLayout('subtask/remove', array(
            'subtask' => $subtask,
            'task' => $task,
        )));
    }

    /**
     * Remove a subtask
     *
     * @access public
     */
    public function remove()
    {
        $this->checkCSRFParam();
        $task = $this->getTask();
        $subtask = $this->getSubtask();

        if ($this->subtask->remove($subtask['id'])) {
            $this->session->flash(t('Sub-task removed successfully.'));
        } else {
            $this->session->flashError(t('Unable to remove this sub-task.'));
        }

        $this->response->redirect($this->helper->url->to('task', 'show', array('project_id' => $task['project_id'], 'task_id' => $task['id']), 'subtasks'));
    }

    /**
     * Change status to the next status: Toto -> In Progress -> Done
     *
     * @access public
     */
    public function toggleStatus()
    {
        $task = $this->getTask();
        $subtask = $this->getSubtask();
        $redirect = $this->request->getStringParam('redirect', 'task');

        $this->subtask->toggleStatus($subtask['id']);

        if ($redirect === 'board') {
            $this->session['has_subtask_inprogress'] = $this->subtask->hasSubtaskInProgress($this->userSession->getId());

            $this->response->html($this->template->render('board/tooltip_subtasks', array(
                'subtasks' => $this->subtask->getAll($task['id']),
                'task' => $task,
            )));
        }

        $this->toggleRedirect($task, $redirect);
    }

    /**
     * Handle subtask restriction (popover)
     *
     * @access public
     */
    public function subtaskRestriction()
    {
        $task = $this->getTask();
        $subtask = $this->getSubtask();

        $this->response->html($this->template->render('subtask/restriction_change_status', array(
            'status_list' => array(
                SubtaskModel::STATUS_TODO => t('Todo'),
                SubtaskModel::STATUS_DONE => t('Done'),
            ),
            'subtask_inprogress' => $this->subtask->getSubtaskInProgress($this->userSession->getId()),
            'subtask' => $subtask,
            'task' => $task,
            'redirect' => $this->request->getStringParam('redirect'),
        )));
    }

    /**
     * Change status of the in progress subtask and the other subtask
     *
     * @access public
     */
    public function changeRestrictionStatus()
    {
        $task = $this->getTask();
        $subtask = $this->getSubtask();
        $values = $this->request->getValues();

        // Change status of the previous in progress subtask
        $this->subtask->update(array(
            'id' => $values['id'],
            'status' => $values['status'],
        ));

        // Set the current subtask to in pogress
        $this->subtask->update(array(
            'id' => $subtask['id'],
            'status' => SubtaskModel::STATUS_INPROGRESS,
        ));

        $this->toggleRedirect($task, $values['redirect']);
    }

    /**
     * Redirect to the right page
     *
     * @access private
     */
    private function toggleRedirect(array $task, $redirect)
    {
        switch ($redirect) {
            case 'board':
                $this->response->redirect($this->helper->url->to('board', 'show', array('project_id' => $task['project_id'])));
            case 'dashboard':
                $this->response->redirect($this->helper->url->to('app', 'index'));
            default:
                $this->response->redirect($this->helper->url->to('task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']), 'subtasks'));
        }
    }

    /**
     * Move subtask position
     *
     * @access public
     */
    public function movePosition()
    {
        $this->checkCSRFParam();
        $project_id = $this->request->getIntegerParam('project_id');
        $task_id = $this->request->getIntegerParam('task_id');
        $subtask_id = $this->request->getIntegerParam('subtask_id');
        $direction = $this->request->getStringParam('direction');
        $method = $direction === 'up' ? 'moveUp' : 'moveDown';

        $this->subtask->$method($task_id, $subtask_id);
        $this->response->redirect($this->helper->url->to('task', 'show', array('project_id' => $project_id, 'task_id' => $task_id), 'subtasks'));
    }
}
