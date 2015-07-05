<?php

namespace Model;

/**
 * Task Analytic
 *
 * @package  model
 * @author   Frederic Guillot
 */
class TaskAnalytic extends Base
{
    /**
     * Get the time between date_creation and date_completed or now if empty
     *
     * @access public
     * @param  array   $task
     * @return integer
     */
    public function getLeadTime(array $task)
    {
        return ($task['date_completed'] ?: time()) - $task['date_creation'];
    }

    /**
     * Get the time between date_started and date_completed or now if empty
     *
     * @access public
     * @param  array   $task
     * @return integer
     */
    public function getCycleTime(array $task)
    {
        if (empty($task['date_started'])) {
            return 0;
        }

        return ($task['date_completed'] ?: time()) - $task['date_started'];
    }

    /**
     * Get the average time spent in each column
     *
     * @access public
     * @param  array   $task
     * @return array
     */
    public function getAverageTimeByColumn(array $task)
    {
        $result = array();
        $columns = $this->board->getColumnsList($task['project_id']);
        $averages = $this->transition->getAverageTimeSpentByTask($task['id']);

        foreach ($columns as $column_id => $column_title) {

            $time_spent = 0;

            if (empty($averages) && $task['column_id'] == $column_id) {
                $time_spent = time() - $task['date_creation'];
            }
            else {
                $time_spent = isset($averages[$column_id]) ? $averages[$column_id] : 0;
            }

            $result[] = array(
                'id' => $column_id,
                'title' => $column_title,
                'time_spent' => $time_spent,
            );
        }

        return $result;
    }
}
