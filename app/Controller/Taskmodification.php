<?php

namespace Kanboard\Controller;

/**
 * Task Modification controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class Taskmodification extends Base
{
    /**
     * Set automatically the start date
     *
     * @access public
     */
    public function start()
    {
        $task = $this->getTask();
        $this->taskModification->update(array('id' => $task['id'], 'date_started' => time()));
        $this->response->redirect($this->helper->url->to('task', 'show', array('project_id' => $task['project_id'], 'task_id' => $task['id'])));
    }

    /**
     * Update time tracking information
     *
     * @access public
     */
    public function time()
    {
        $task = $this->getTask();
        $values = $this->request->getValues();

        list($valid, ) = $this->taskValidator->validateTimeModification($values);

        if ($valid && $this->taskModification->update($values)) {
            $this->flash->success(t('Task updated successfully.'));
        } else {
            $this->flash->failure(t('Unable to update your task.'));
        }

        $this->response->redirect($this->helper->url->to('task', 'show', array('project_id' => $task['project_id'], 'task_id' => $task['id'])));
    }

    /**
     * Edit description form
     *
     * @access public
     */
    public function description()
    {
        $task = $this->getTask();
        $redirect = $this->request->getStringParam('redirect', 'task');

        if ($this->request->isPost()) {
            $values = $this->request->getValues();

            list($valid, $errors) = $this->taskValidator->validateDescriptionCreation($values);

            if ($valid) {
                if ($this->taskModification->update($values)) {
                    $this->flash->success(t('Task updated successfully.'));
                } else {
                    $this->flash->failure(t('Unable to update your task.'));
                }

                $this->redirect(
                    $values,
                    $redirect
                );
            }
        } else {
            $values = $task;
            $errors = array();
        }

        $params = array(
            'values' => $values,
            'errors' => $errors,
            'task' => $task,
            'redirect' => $redirect,
        );

        if ($this->request->isAjax()) {
            $this->response->html($this->template->render('task_modification/edit_description', $params));
        } else {
            $this->response->html($this->taskLayout('task_modification/edit_description', $params));
        }
    }

    /**
     * Display a form to edit a task
     *
     * @access public
     */
    public function edit(array $values = array(), array $errors = array())
    {
        $task = $this->getTask();
        $project = $this->project->getById($task['project_id']);
        $redirect = $this->request->getStringParam('redirect', 'task');

        if (empty($values)) {
            $values = $task;
        }

        $this->dateParser->format($values, array('date_due'));

        $params = array(
            'project' => $project,
            'values' => $values,
            'errors' => $errors,
            'task' => $task,
            'users_list' => $this->projectUserRole->getAssignableUsersList($task['project_id']),
            'colors_list' => $this->color->getList(),
            'categories_list' => $this->category->getList($task['project_id']),
            'date_format' => $this->config->get('application_date_format'),
            'date_formats' => $this->dateParser->getAvailableFormats(),
            'redirect' => $redirect,
        );

        if ($this->request->isAjax()) {
            $html = $this->template->render('task_modification/edit_task', $params);
        } else {
            $html = $this->taskLayout('task_modification/edit_task', $params);
        }

        $this->response->html($html);
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

        if ($valid && $this->taskModification->update($values)) {
            $this->flash->success(t('Task updated successfully.'));

            $this->redirect(
                $task,
                $this->request->getStringParam('redirect', 'task')
            );
        } else {
            $this->flash->failure(t('Unable to update your task.'));
            $this->edit($values, $errors);
        }
    }

    /**
     * Edit recurrence form
     *
     * @access public
     */
    public function recurrence()
    {
        $task = $this->getTask();
        $redirect = $this->request->getStringParam('redirect', 'task');

        if ($this->request->isPost()) {
            $values = $this->request->getValues();

            list($valid, $errors) = $this->taskValidator->validateEditRecurrence($values);

            if ($valid) {
                if ($this->taskModification->update($values)) {
                    $this->flash->success(t('Task updated successfully.'));
                } else {
                    $this->flash->failure(t('Unable to update your task.'));
                }

                $this->redirect(
                    $task,
                    $redirect
                );
            }
        } else {
            $values = $task;
            $errors = array();
        }

        $params = array(
            'values' => $values,
            'errors' => $errors,
            'task' => $task,
            'recurrence_status_list' => $this->task->getRecurrenceStatusList(),
            'recurrence_trigger_list' => $this->task->getRecurrenceTriggerList(),
            'recurrence_timeframe_list' => $this->task->getRecurrenceTimeframeList(),
            'recurrence_basedate_list' => $this->task->getRecurrenceBasedateList(),
            'redirect' => $redirect
        );

        $this->response->html($this->taskLayout('task_modification/edit_recurrence', $params));
    }
}
