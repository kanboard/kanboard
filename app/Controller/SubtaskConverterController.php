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
        $project = $this->getProject();
        $task = $this->getTask();
        $subtask = $this->getSubtask($task);

        $task_id = $this->subtaskTaskConversionModel->convertToTask($project['id'], $subtask['id']);

        if ($task_id !== false) {
            $this->flash->success(t('Subtask converted to task successfully.'));
        } else {
            $this->flash->failure(t('Unable to convert the subtask.'));
        }

        $this->response->redirect($this->helper->url->to('TaskViewController', 'show', array('project_id' => $project['id'], 'task_id' => $task_id)), true);
    }
}
