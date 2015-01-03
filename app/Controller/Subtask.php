<?php

namespace Controller;

/**
 * SubTask controller
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
        $subtask = $this->subTask->getById($this->request->getIntegerParam('subtask_id'));

        if (! $subtask) {
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

        list($valid, $errors) = $this->subTask->validateCreation($values);

        if ($valid) {

            if ($this->subTask->create($values)) {
                $this->session->flash(t('Sub-task added successfully.'));
            }
            else {
                $this->session->flashError(t('Unable to create your sub-task.'));
            }

            if (isset($values['another_subtask']) && $values['another_subtask'] == 1) {
                $this->response->redirect('?controller=subtask&action=create&task_id='.$task['id'].'&another_subtask=1&project_id='.$task['project_id']);
            }

            $this->response->redirect('?controller=task&action=show&task_id='.$task['id'].'&project_id='.$task['project_id'].'#subtasks');
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
            'status_list' => $this->subTask->getStatusList(),
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
        list($valid, $errors) = $this->subTask->validateModification($values);

        if ($valid) {

            if ($this->subTask->update($values)) {
                $this->session->flash(t('Sub-task updated successfully.'));
            }
            else {
                $this->session->flashError(t('Unable to update your sub-task.'));
            }

            $this->response->redirect('?controller=task&action=show&task_id='.$task['id'].'&project_id='.$task['project_id'].'#subtasks');
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

        if ($this->subTask->remove($subtask['id'])) {
            $this->session->flash(t('Sub-task removed successfully.'));
        }
        else {
            $this->session->flashError(t('Unable to remove this sub-task.'));
        }

        $this->response->redirect('?controller=task&action=show&task_id='.$task['id'].'&project_id='.$task['project_id'].'#subtasks');
    }

    /**
     * Change status to the next status: Toto -> In Progress -> Done
     *
     * @access public
     */
    public function toggleStatus()
    {
        $task = $this->getTask();
        $subtask_id = $this->request->getIntegerParam('subtask_id');

        if (! $this->subTask->toggleStatus($subtask_id)) {
            $this->session->flashError(t('Unable to update your sub-task.'));
        }

        $this->response->redirect('?controller=task&action=show&task_id='.$task['id'].'&project_id='.$task['project_id'].'#subtasks');
    }
}
