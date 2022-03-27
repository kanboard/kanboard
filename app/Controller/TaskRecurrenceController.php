<?php

namespace Kanboard\Controller;

/**
 * Task Recurrence controller
 *
 * @package  Kanboard\Controller
 * @author   Frederic Guillot
 */
class TaskRecurrenceController extends BaseController
{
    /**
     * Edit recurrence form
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

        if (empty($values)) {
            $values = $task;
        }

        $this->response->html($this->template->render('task_recurrence/edit', array(
            'values' => $values,
            'errors' => $errors,
            'task' => $task,
            'recurrence_status_list' => $this->taskRecurrenceModel->getRecurrenceStatusList(),
            'recurrence_trigger_list' => $this->taskRecurrenceModel->getRecurrenceTriggerList(),
            'recurrence_timeframe_list' => $this->taskRecurrenceModel->getRecurrenceTimeframeList(),
            'recurrence_basedate_list' => $this->taskRecurrenceModel->getRecurrenceBasedateList(),
        )));
    }

    /**
     * Update recurrence form
     *
     * @access public
     */
    public function update()
    {
        $task = $this->getTask();
        $values = $this->request->getValues();
        $values['id'] = $task['id'];

        list($valid, $errors) = $this->taskValidator->validateEditRecurrence($values);

        if ($valid) {
            if ($this->taskModificationModel->update($values)) {
                $this->flash->success(t('Task updated successfully.'));
            } else {
                $this->flash->failure(t('Unable to update your task.'));
            }

            return $this->response->redirect($this->helper->url->to('TaskViewController', 'show', array('task_id' => $task['id'])), true);
        }

        return $this->edit($values, $errors);
    }
}
