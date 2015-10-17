<?php

namespace Kanboard\Controller;

/**
 * Time Tracking controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class Timer extends Base
{
    /**
     * Start/stop timer for subtasks
     *
     * @access public
     */
    public function subtask()
    {
        $project_id = $this->request->getIntegerParam('project_id');
        $task_id = $this->request->getIntegerParam('task_id');
        $subtask_id = $this->request->getIntegerParam('subtask_id');
        $timer = $this->request->getStringParam('timer');

        if ($timer === 'start') {
            $this->subtaskTimeTracking->logStartTime($subtask_id, $this->userSession->getId());
        } elseif ($timer === 'stop') {
            $this->subtaskTimeTracking->logEndTime($subtask_id, $this->userSession->getId());
            $this->subtaskTimeTracking->updateTaskTimeTracking($task_id);
        }

        $this->response->redirect($this->helper->url->to('task', 'show', array('project_id' => $project_id, 'task_id' => $task_id)).'#subtasks');
    }
}
