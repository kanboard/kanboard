<?php

namespace Kanboard\Formatter;

/**
 * Class TaskListSubtaskAssigneeFormatter
 *
 * @package Kanboard\Formatter
 * @author  Frederic Guillot
 */
class TaskListSubtaskAssigneeFormatter extends TaskListFormatter
{
    protected $userId = 0;

    /**
     * Set assignee
     *
     * @param  integer $userId
     * @return $this
     */
    public function withUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * Apply formatter
     *
     * @access public
     * @return array
     */
    public function format()
    {
        $tasks = parent::format();
        $taskIds = array_column($tasks, 'id');
        $subtasks = $this->subtaskModel->getAllByTaskIdsAndAssignee($taskIds, $this->userId);
        $subtasks = array_column_index($subtasks, 'task_id');
        array_merge_relation($tasks, $subtasks, 'subtasks', 'id');

        return $tasks;
    }
}
