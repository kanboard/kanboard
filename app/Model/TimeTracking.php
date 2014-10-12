<?php

namespace Model;

/**
 * Time tracking model
 *
 * @package  model
 * @author   Frederic Guillot
 */
class TimeTracking extends Base
{
    /**
     * Calculate time metrics for a task
     *
     * Use subtasks time metrics if not empty otherwise return task time metrics
     *
     * @access public
     * @param  array    $task        Task properties
     * @param  array    $subtasks    Subtasks list
     * @return array
     */
    public function getTaskTimesheet(array $task, array $subtasks)
    {
        $timesheet = array(
            'time_spent' => 0,
            'time_estimated' => 0,
            'time_remaining' => 0,
        );

        foreach ($subtasks as &$subtask) {
            $timesheet['time_estimated'] += $subtask['time_estimated'];
            $timesheet['time_spent'] += $subtask['time_spent'];
        }

        if ($timesheet['time_estimated'] == 0 && $timesheet['time_spent'] == 0) {
            $timesheet['time_estimated'] = $task['time_estimated'];
            $timesheet['time_spent'] = $task['time_spent'];
        }

        $timesheet['time_remaining'] = $timesheet['time_estimated'] - $timesheet['time_spent'];

        return $timesheet;
    }
}
