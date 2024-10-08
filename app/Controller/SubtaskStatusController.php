<?php

namespace Kanboard\Controller;

/**
 * Subtask Status
 *
 * @package  Kanboard\Controller
 * @author   Frederic Guillot
 */
class SubtaskStatusController extends BaseController
{
    /**
     * Change status to the next status: Toto -> In Progress -> Done
     *
     * @access public
     */
    public function change()
    {
        $this->checkReusableGETCSRFParam();
        $task = $this->getTask();
        $subtask = $this->getSubtask($task);
        $fragment = $this->request->getStringParam('fragment');

        $status = $this->subtaskStatusModel->toggleStatus($subtask['id']);
        $subtask['status'] = $status;

        if ($fragment === 'table') {
            $html = $this->renderTable($task);
        } elseif ($fragment === 'rows') {
            $html = $this->renderRows($task);
        } else {
            $html = $this->helper->subtask->renderToggleStatus($task, $subtask);
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
        $this->checkReusableGETCSRFParam();
        $task = $this->getTask();
        $subtask = $this->getSubtask($task);
        $timer = $this->request->getStringParam('timer');

        if ($timer === 'start') {
            $this->subtaskTimeTrackingModel->logStartTime($subtask['id'], $this->userSession->getId());
        } elseif ($timer === 'stop') {
            $this->subtaskTimeTrackingModel->logEndTime($subtask['id'], $this->userSession->getId());
            $this->subtaskTimeTrackingModel->updateTaskTimeTracking($task['id']);
        }

        $this->response->html($this->template->render('subtask/timer', array(
            'task'    => $task,
            'subtask' => $this->subtaskModel->getByIdWithDetails($subtask['id']),
        )));
    }

    /**
     * Render table
     *
     * @access protected
     * @param  array  $task
     * @return string
     */
    protected function renderTable(array $task)
    {
        return $this->template->render('subtask/table', array(
            'task'     => $task,
            'subtasks' => $this->subtaskModel->getAll($task['id']),
            'editable' => true,
        ));
    }

    /**
     * Render task list rows
     *
     * @access protected
     * @param  array  $task
     * @return string
     */
    protected function renderRows(array $task)
    {
        $userId = $this->request->getIntegerParam('user_id');

        if ($userId > 0) {
            $task['subtasks'] = $this->subtaskModel->getAllByTaskIdsAndAssignee(array($task['id']), $userId);
        } else {
            $task['subtasks'] = $this->subtaskModel->getAll($task['id']);
        }

        return $this->template->render('task_list/task_subtasks', array(
            'task'    => $task,
            'user_id' => $userId,
        ));
    }
}
