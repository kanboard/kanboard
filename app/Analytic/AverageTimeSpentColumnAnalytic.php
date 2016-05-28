<?php

namespace Kanboard\Analytic;

use Kanboard\Core\Base;
use Kanboard\Model\TaskModel;

/**
 * Average Time Spent by Column
 *
 * @package  analytic
 * @author   Frederic Guillot
 */
class AverageTimeSpentColumnAnalytic extends Base
{
    /**
     * Build report
     *
     * @access public
     * @param  integer   $project_id    Project id
     * @return array
     */
    public function build($project_id)
    {
        $stats = $this->initialize($project_id);

        $this->processTasks($stats, $project_id);
        $this->calculateAverage($stats);

        return $stats;
    }

    /**
     * Initialize default values for each column
     *
     * @access private
     * @param  integer $project_id
     * @return array
     */
    private function initialize($project_id)
    {
        $stats = array();
        $columns = $this->columnModel->getList($project_id);

        foreach ($columns as $column_id => $column_title) {
            $stats[$column_id] = array(
                'count' => 0,
                'time_spent' => 0,
                'average' => 0,
                'title' => $column_title,
            );
        }

        return $stats;
    }

    /**
     * Calculate time spent for each tasks for each columns
     *
     * @access private
     * @param  array   $stats
     * @param  integer $project_id
     */
    private function processTasks(array &$stats, $project_id)
    {
        $tasks = $this->getTasks($project_id);

        foreach ($tasks as &$task) {
            foreach ($this->getTaskTimeByColumns($task) as $column_id => $time_spent) {
                if (isset($stats[$column_id])) {
                    $stats[$column_id]['count']++;
                    $stats[$column_id]['time_spent'] += $time_spent;
                }
            }
        }
    }

    /**
     * Calculate averages
     *
     * @access private
     * @param  array   $stats
     */
    private function calculateAverage(array &$stats)
    {
        foreach ($stats as &$column) {
            $this->calculateColumnAverage($column);
        }
    }

    /**
     * Calculate column average
     *
     * @access private
     * @param  array   $column
     */
    private function calculateColumnAverage(array &$column)
    {
        if ($column['count'] > 0) {
            $column['average'] = (int) ($column['time_spent'] / $column['count']);
        }
    }

    /**
     * Get time spent for each column for a given task
     *
     * @access private
     * @param  array   $task
     * @return array
     */
    private function getTaskTimeByColumns(array &$task)
    {
        $columns = $this->transitionModel->getTimeSpentByTask($task['id']);

        if (! isset($columns[$task['column_id']])) {
            $columns[$task['column_id']] = 0;
        }

        $columns[$task['column_id']] += $this->getTaskTimeSpentInCurrentColumn($task);

        return $columns;
    }

    /**
     * Calculate time spent of a task in the current column
     *
     * @access private
     * @param  array   $task
     * @return integer
     */
    private function getTaskTimeSpentInCurrentColumn(array &$task)
    {
        $end = $task['date_completed'] ?: time();
        return $end - $task['date_moved'];
    }

    /**
     * Fetch the last 1000 tasks
     *
     * @access private
     * @param  integer $project_id
     * @return array
     */
    private function getTasks($project_id)
    {
        return $this->db
            ->table(TaskModel::TABLE)
            ->columns('id', 'date_completed', 'date_moved', 'column_id')
            ->eq('project_id', $project_id)
            ->desc('id')
            ->limit(1000)
            ->findAll();
    }
}
