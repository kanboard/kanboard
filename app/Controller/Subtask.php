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
    public function create()
    {
        $task = $this->getTask();

        $this->response->html($this->taskLayout('subtask_create', array(
            'values' => array(
                'task_id' => $task['id'],
                'another_subtask' => $this->request->getIntegerParam('another_subtask', 0)
            ),
            'errors' => array(),
            'users_list' => $this->projectPermission->getUsersList($task['project_id']),
            'task' => $task,
            'menu' => 'tasks',
            'title' => t('Add a sub-task')
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
                $this->response->redirect('?controller=subtask&action=create&task_id='.$task['id'].'&another_subtask=1');
            }

            $this->response->redirect('?controller=task&action=show&task_id='.$task['id'].'#subtasks');
        }

        $this->response->html($this->taskLayout('subtask_create', array(
            'values' => $values,
            'errors' => $errors,
            'users_list' => $this->projectPermission->getUsersList($task['project_id']),
            'task' => $task,
            'menu' => 'tasks',
            'title' => t('Add a sub-task')
        )));
    }

    /**
     * Edit form
     *
     * @access public
     */
    public function edit()
    {
        $task = $this->getTask();
        $subtask = $this->getSubTask();

        $this->response->html($this->taskLayout('subtask_edit', array(
            'values' => $subtask,
            'errors' => array(),
            'users_list' => $this->projectPermission->getUsersList($task['project_id']),
            'status_list' => $this->subTask->getStatusList(),
            'subtask' => $subtask,
            'task' => $task,
            'menu' => 'tasks',
            'title' => t('Edit a sub-task')
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
        $subtask = $this->getSubtask();

        $values = $this->request->getValues();
        list($valid, $errors) = $this->subTask->validateModification($values);

        if ($valid) {

            if ($this->subTask->update($values)) {
                $this->session->flash(t('Sub-task updated successfully.'));
            }
            else {
                $this->session->flashError(t('Unable to update your sub-task.'));
            }

            $this->response->redirect('?controller=task&action=show&task_id='.$task['id'].'#subtasks');
        }

        $this->response->html($this->taskLayout('subtask_edit', array(
            'values' => $values,
            'errors' => $errors,
            'users_list' => $this->projectPermission->getUsersList($task['project_id']),
            'status_list' => $this->subTask->getStatusList(),
            'subtask' => $subtask,
            'task' => $task,
            'menu' => 'tasks',
            'title' => t('Edit a sub-task')
        )));
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

        $this->response->html($this->taskLayout('subtask_remove', array(
            'subtask' => $subtask,
            'task' => $task,
            'menu' => 'tasks',
            'title' => t('Remove a sub-task')
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

        $this->response->redirect('?controller=task&action=show&task_id='.$task['id'].'#subtasks');
    }

    /**
     * Toggle status
     * Change status to the next status: Toto -> In Progress -> Done
     * @access public
     */
    public function toggleStatus()
    {
        $subtask = $this->getSubtask();

        $value = array( 'id' => $subtask['id'],
                        'status' => ($subtask['status'] + 1) % 3 );

        $task = $this->getTask();
        if (!$this->subTask->update($value)) {
            $this->session->flashError(t('Unable to change sub-task state.'));
        }

        $this->response->redirect('?controller=task&action=show&task_id='.$task['id'].'#subtasks');      
    }
}
