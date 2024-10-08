<?php

namespace Kanboard\Controller;

/**
 * Class SubtaskConverterController
 *
 * @package Kanboard\Controller
 * @author  Frederic Guillot
 */
class SubtaskConverterController extends BaseController
{
    public function show()
    {
        $task = $this->getTask();
        $subtask = $this->getSubtask($task);

        $this->response->html($this->template->render('subtask_converter/show', array(
            'subtask' => $subtask,
            'task' => $task,
        )));
    }

    public function save()
    {
        $task = $this->getTask();
        $subtask = $this->getSubtask($task);

        if (! $this->helper->projectRole->canCreateTaskInColumn($task['project_id'], $task['column_id'])) {
            $this->flash->failure(t('You cannot create tasks in this column.'));
            $this->response->redirect($this->helper->url->to('TaskViewController', 'show', ['task_id' => $task['id']]));
            return;
        }

        $task_id = $this->subtaskTaskConversionModel->convertToTask($task['project_id'], $subtask['id']);

        if ($task_id !== false) {
            $this->flash->success(t('Subtask converted to task successfully.'));
        } else {
            $this->flash->failure(t('Unable to convert the subtask.'));
        }

        $this->response->redirect($this->helper->url->to('TaskViewController', 'show', array('task_id' => $task_id)), true);
    }
}
