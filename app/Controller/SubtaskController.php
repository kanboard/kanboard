<?php

namespace Kanboard\Controller;

use Kanboard\Core\Controller\AccessForbiddenException;
use Kanboard\Core\Controller\PageNotFoundException;

/**
 * Subtask controller
 *
 * @package  Kanboard\Controller
 * @author   Frederic Guillot
 */
class SubtaskController extends BaseController
{
    /**
     * Creation form
     *
     * @access public
     * @param array $values
     * @param array $errors
     * @throws AccessForbiddenException
     * @throws PageNotFoundException
     */
    public function create(array $values = array(), array $errors = array())
    {
        $task = $this->getTask();

        if (empty($values)) {
            $values = $this->prepareValues($task);
        }

        $this->response->html($this->template->render('subtask/create', array(
            'values' => $values,
            'errors' => $errors,
            'users_list' => $this->projectUserRoleModel->getAssignableUsersList($task['project_id']),
            'task' => $task,
        )));
    }
    
    /**
     * Prepare form values
     *
     * @access protected
     * @param  array $task
     * @return array
     */
    protected function prepareValues(array $task)
    {
        $values = array(
            'task_id' => $task['id'],
            'user_id' => $task['owner_id'],
            'another_subtask' => $this->request->getIntegerParam('another_subtask', 0)
        );

        $values = $this->hook->merge('controller:subtask:form:default', $values, array('default_values' => $values));
        return $values;
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
        $values['task_id'] = $task['id'];
        $subtasks = explode("\r\n", isset($values['title']) ? $values['title'] : '');
        $subtasksAdded = 0;

        foreach ($subtasks as $subtask) {
            $subtask = trim($subtask);

            if (! empty($subtask)) {
                $subtaskValues = $values;
                $subtaskValues['title'] = $subtask;

                list($valid, $errors) = $this->subtaskValidator->validateCreation($subtaskValues);

                if (! $valid) {
                    $this->create($values, $errors);
                    return false;
                }

                if (! $this->subtaskModel->create($subtaskValues)) {
                    $this->flash->failure(t('Unable to create your sub-task.'));
                    $this->response->redirect($this->helper->url->to('TaskViewController', 'show', array('project_id' => $task['project_id'], 'task_id' => $task['id']), 'subtasks'), true);
                    return false;
                }

                $subtasksAdded++;
            }
        }

        if (isset($values['another_subtask']) && $values['another_subtask'] == 1) {
            return $this->create(array(
                'project_id' => $task['project_id'],
                'task_id' => $task['id'],
                'user_id' => $values['user_id'],
                'another_subtask' => 1,
                'subtasks_added' => $subtasksAdded,
            ));
        } else if ($subtasksAdded > 0) {
            if ($subtasksAdded === 1) {
                $this->flash->success(t('Subtask added successfully.'));
            } else {
                $this->flash->success(t('%d subtasks added successfully.', $subtasksAdded));
            }
        }

        $this->response->redirect($this->helper->url->to('TaskViewController', 'show', array('project_id' => $task['project_id'], 'task_id' => $task['id']), 'subtasks'), true);
    }

    /**
     * Edit form
     *
     * @access public
     * @param array $values
     * @param array $errors
     * @throws AccessForbiddenException
     * @throws PageNotFoundException
     */
    public function edit(array $values = array(), array $errors = array())
    {
        $task = $this->getTask();
        $subtask = $this->getSubtask($task);

        $this->response->html($this->template->render('subtask/edit', array(
            'values' => empty($values) ? $subtask : $values,
            'errors' => $errors,
            'users_list' => $this->projectUserRoleModel->getAssignableUsersList($task['project_id']),
            'status_list' => $this->subtaskModel->getStatusList(),
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
        $subtask = $this->getSubtask($task);

        $values = $this->request->getValues();
        $values['id'] = $subtask['id'];
        $values['task_id'] = $task['id'];

        list($valid, $errors) = $this->subtaskValidator->validateModification($values);

        if ($valid) {
            if ($this->subtaskModel->update($values)) {
                $this->flash->success(t('Sub-task updated successfully.'));
            } else {
                $this->flash->failure(t('Unable to update your sub-task.'));
            }

            return $this->response->redirect($this->helper->url->to('TaskViewController', 'show', array('project_id' => $task['project_id'], 'task_id' => $task['id'])), true);
        }

        return $this->edit($values, $errors);
    }

    /**
     * Confirmation dialog before removing a subtask
     *
     * @access public
     */
    public function confirm()
    {
        $task = $this->getTask();
        $subtask = $this->getSubtask($task);

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
        $subtask = $this->getSubtask($task);

        if ($this->subtaskModel->remove($subtask['id'])) {
            $this->flash->success(t('Sub-task removed successfully.'));
        } else {
            $this->flash->failure(t('Unable to remove this sub-task.'));
        }

        $this->response->redirect($this->helper->url->to('TaskViewController', 'show', array('project_id' => $task['project_id'], 'task_id' => $task['id'])), true);
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

        if (! empty($values) && $this->helper->user->hasProjectAccess('SubtaskController', 'movePosition', $project_id)) {
            $result = $this->subtaskPositionModel->changePosition($task_id, $values['subtask_id'], $values['position']);
            $this->response->json(array('result' => $result));
        } else {
            throw new AccessForbiddenException();
        }
    }
}
