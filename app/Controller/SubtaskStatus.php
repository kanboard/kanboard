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
        $subtask['status'] = $status;

        $this->response->html($this->helper->subtask->toggleStatus($subtask, $task['project_id']));
    }
}
