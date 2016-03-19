<?php

namespace Kanboard\Controller;

/**
 * Subtask controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class Subtask extends Base
{
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

        $this->response->html($this->template->render('subtask/create', array(
            'values' => $values,
            'errors' => $errors,
            'users_list' => $this->projectUserRole->getAssignableUsersList($task['project_id']),
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

        list($valid, $errors) = $this->subtaskValidator->validateCreation($values);

        if ($valid) {
            if ($this->subtask->create($values)) {
                $this->flash->success(t('Sub-task added successfully.'));
            } else {
                $this->flash->failure(t('Unable to create your sub-task.'));
            }

            if (isset($values['another_subtask']) && $values['another_subtask'] == 1) {
                return $this->create(array('project_id' => $task['project_id'], 'task_id' => $task['id'], 'another_subtask' => 1));
            }

            return $this->response->redirect($this->helper->url->to('task', 'show', array('project_id' => $task['project_id'], 'task_id' => $task['id']), 'subtasks'), true);
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

        $this->response->html($this->template->render('subtask/edit', array(
            'values' => empty($values) ? $subtask : $values,
            'errors' => $errors,
            'users_list' => $this->projectUserRole->getAssignableUsersList($task['project_id']),
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
        list($valid, $errors) = $this->subtaskValidator->validateModification($values);

        if ($valid) {
            if ($this->subtask->update($values)) {
                $this->flash->success(t('Sub-task updated successfully.'));
            } else {
                $this->flash->failure(t('Unable to update your sub-task.'));
            }

            return $this->response->redirect($this->helper->url->to('task', 'show', array('project_id' => $task['project_id'], 'task_id' => $task['id'])), true);
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

        $this->response->html($this->template->render('subtask/remove', array(
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
            $this->flash->success(t('Sub-task removed successfully.'));
        } else {
            $this->flash->failure(t('Unable to remove this sub-task.'));
        }

        $this->response->redirect($this->helper->url->to('task', 'show', array('project_id' => $task['project_id'], 'task_id' => $task['id'])), true);
    }

    /**
     * Move subtask position
     *
     * @access public
     */
    public function movePosition()
    {
        $project_id = $this->request->getIntegerParam('project_id');
        $task_id = $this->request->getIntegerParam('task_id');
        $values = $this->request->getJson();

        if (! empty($values) && $this->helper->user->hasProjectAccess('Subtask', 'movePosition', $project_id)) {
            $result = $this->subtask->changePosition($task_id, $values['subtask_id'], $values['position']);
            return $this->response->json(array('result' => $result));
        }

        $this->forbidden();
    }
}
