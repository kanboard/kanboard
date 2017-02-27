<?php

namespace Kanboard\Formatter;

/**
 * Class TaskListSubtaskFormatter
 *
 * @package Kanboard\Formatter
 * @author  Frederic Guillot
 */
class TaskListSubtaskFormatter extends TaskListFormatter
{
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
        $subtasks = $this->subtaskModel->getAllByTaskIds($taskIds);
        $subtasks = array_column_index($subtasks, 'task_id');
        array_merge_relation($tasks, $subtasks, 'subtasks', 'id');

        return $tasks;
    }
}
