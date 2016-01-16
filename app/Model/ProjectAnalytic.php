<?php

namespace Kanboard\Model;

/**
 * Project analytic model
 *
 * @package  model
 * @author   Frederic Guillot
 */
class ProjectAnalytic extends Base
{
    /**
     * Get the average lead and cycle time
     *
     * @access public
     * @param  integer   $project_id
     * @return array
     */
    public function getAverageLeadAndCycleTime($project_id)
    {
        $stats = array(
            'count' => 0,
            'total_lead_time' => 0,
            'total_cycle_time' => 0,
            'avg_lead_time' => 0,
            'avg_cycle_time' => 0,
        );

        $tasks = $this->db
            ->table(Task::TABLE)
            ->columns('date_completed', 'date_creation', 'date_started')
            ->eq('project_id', $project_id)
            ->desc('id')
            ->limit(1000)
            ->findAll();

        foreach ($tasks as &$task) {
            $stats['count']++;
            $stats['total_lead_time'] += ($task['date_completed'] ?: time()) - $task['date_creation'];
            $stats['total_cycle_time'] += empty($task['date_started']) ? 0 : ($task['date_completed'] ?: time()) - $task['date_started'];
        }

        $stats['avg_lead_time'] = $stats['count'] > 0 ? (int) ($stats['total_lead_time'] / $stats['count']) : 0;
        $stats['avg_cycle_time'] = $stats['count'] > 0 ? (int) ($stats['total_cycle_time'] / $stats['count']) : 0;

        return $stats;
    }

    /**
     * Get the average time spent into each column
     *
     * @access public
     * @param  integer   $project_id
     * @return array
     */
    public function getAverageTimeSpentByColumn($project_id)
    {
        $stats = array();
        $columns = $this->board->getColumnsList($project_id);

        // Get the time spent of the last move for each tasks
        $tasks = $this->db
            ->table(Task::TABLE)
            ->columns('id', 'date_completed', 'date_moved', 'column_id')
            ->eq('project_id', $project_id)
            ->desc('id')
            ->limit(1000)
            ->findAll();

        // Init values
        foreach ($columns as $column_id => $column_title) {
            $stats[$column_id] = array(
                'count' => 0,
                'time_spent' => 0,
                'average' => 0,
                'title' => $column_title,
            );
        }

        // Get time spent foreach task/column and take into account the last move
        foreach ($tasks as &$task) {
            $sums = $this->transition->getTimeSpentByTask($task['id']);

            if (! isset($sums[$task['column_id']])) {
                $sums[$task['column_id']] = 0;
            }

            $sums[$task['column_id']] += ($task['date_completed'] ?: time()) - $task['date_moved'];

            foreach ($sums as $column_id => $time_spent) {
                if (isset($stats[$column_id])) {
                    $stats[$column_id]['count']++;
                    $stats[$column_id]['time_spent'] += $time_spent;
                }
            }
        }

        // Calculate average for each column
        foreach ($columns as $column_id => $column_title) {
            $stats[$column_id]['average'] = $stats[$column_id]['count'] > 0 ? (int) ($stats[$column_id]['time_spent'] / $stats[$column_id]['count']) : 0;
        }

        return $stats;
    }
}
