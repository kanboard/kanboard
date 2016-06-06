<?php

namespace Kanboard\Controller;

/**
 * Task Modification controller
 *
 * @package  Kanboard\Controller
 * @author   Frederic Guillot
 */
class TaskModificationController extends BaseController
{
    /**
     * Set automatically the start date
     *
     * @access public
     */
    public function start()
    {
        $task = $this->getTask();
        $this->taskModificationModel->update(array('id' => $task['id'], 'date_started' => time()));
        $this->response->redirect($this->helper->url->to('TaskViewController', 'show', array('project_id' => $task['project_id'], 'task_id' => $task['id'])));
    }

    /**
     * Edit description form
     *
     * @access public
     * @param array $values
     * @param array $errors
     * @throws \Kanboard\Core\Controller\AccessForbiddenException
     * @throws \Kanboard\Core\Controller\PageNotFoundException
     */
    public function description(array $values = array(), array $errors = array())
    {
        $task = $this->getTask();

        if (empty($values)) {
            $values = array('id' => $task['id'], 'description' => $task['description']);
        }

        $this->response->html($this->template->render('task_modification/edit_description', array(
            'values' => $values,
            'errors' => $errors,
            'task' => $task,
        )));
    }

    /**
     * Update description
     *
     * @access public
     */
    public function updateDescription()
    {
        $task = $this->getTask();
        $values = $this->request->getValues();

        list($valid, $errors) = $this->taskValidator->validateDescriptionCreation($values);

        if ($valid) {
            if ($this->taskModificationModel->update($values)) {
                $this->flash->success(t('Task updated successfully.'));
            } else {
                $this->flash->failure(t('Unable to update your task.'));
            }

            return $this->response->redirect($this->helper->url->to('TaskViewController', 'show', array('project_id' => $task['project_id'], 'task_id' => $task['id'])), true);
        }

        return $this->description($values, $errors);
    }

    /**
     * Display a form to edit a task
     *
     * @access public
     * @param array $values
     * @param array $errors
     * @throws \Kanboard\Core\Controller\AccessForbiddenException
     * @throws \Kanboard\Core\Controller\PageNotFoundException
     */
    public function edit(array $values = array(), array $errors = array())
    {
        $task = $this->getTask();
        $project = $this->projectModel->getById($task['project_id']);

        if (empty($values)) {
            $values = $task;
            $values = $this->hook->merge('controller:task:form:default', $values, array('default_values' => $values));
            $values = $this->hook->merge('controller:task-modification:form:default', $values, array('default_values' => $values));
            $values = $this->dateParser->format($values, array('date_due'), $this->dateParser->getUserDateFormat());
            $values = $this->dateParser->format($values, array('date_started'), $this->dateParser->getUserDateTimeFormat());
        }

        $this->response->html($this->template->render('task_modification/edit_task', array(
            'project' => $project,
            'values' => $values,
            'errors' => $errors,
            'task' => $task,
            'users_list' => $this->projectUserRoleModel->getAssignableUsersList($task['project_id']),
            'colors_list' => $this->colorModel->getList(),
            'categories_list' => $this->categoryModel->getList($task['project_id']),
        )));
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

        if ($valid && $this->taskModificationModel->update($values)) {
            $this->flash->success(t('Task updated successfully.'));
            $this->response->redirect($this->helper->url->to('TaskViewController', 'show', array('project_id' => $task['project_id'], 'task_id' => $task['id'])), true);
        } else {
            $this->flash->failure(t('Unable to update your task.'));
            $this->edit($values, $errors);
        }
    }
}
