<?php

namespace Kanboard\Controller;

/**
 * Subtask Status
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class SubtaskStatus extends Base
{
    /**
     * Change status to the next status: Toto -> In Progress -> Done
     *
     * @access public
     */
    public function change()
    {
        $task = $this->getTask();
        $subtask = $this->getSubtask();

        $status = $this->subtask->toggleStatus($subtask['id']);

        if ($this->request->getIntegerParam('refresh-table') === 0) {
            $subtask['status'] = $status;
            $html = $this->helper->subtask->toggleStatus($subtask, $task['project_id']);
        } else {
            $html = $this->renderTable($task);
        }

        $this->response->html($html);
    }

    /**
     * Start/stop timer for subtasks
     *
     * @access public
     */
    public function timer()
    {
        $task = $this->getTask();
        $subtask_id = $this->request->getIntegerParam('subtask_id');
        $timer = $this->request->getStringParam('timer');

        if ($timer === 'start') {
            $this->subtaskTimeTracking->logStartTime($subtask_id, $this->userSession->getId());
        } elseif ($timer === 'stop') {
            $this->subtaskTimeTracking->logEndTime($subtask_id, $this->userSession->getId());
            $this->subtaskTimeTracking->updateTaskTimeTracking($task['id']);
        }

        $this->response->html($this->renderTable($task));
    }

    /**
     * Render table
     *
     * @access private
     * @param  array  $task
     * @return string
     */
    private function renderTable(array $task)
    {
        return $this->template->render('subtask/table', array(
            'task' => $task,
            'subtasks' => $this->subtask->getAll($task['id']),
            'editable' => true,
            'redirect' => 'task',
        ));
    }
}
