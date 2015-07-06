<?php

namespace Model;

/**
 * Project analytic model
 *
 * @package  model
 * @author   Frederic Guillot
 */
class ProjectAnalytic extends Base
{
    /**
     * Get tasks repartition
     *
     * @access public
     * @param  integer   $project_id    Project id
     * @return array
     */
    public function getTaskRepartition($project_id)
    {
        $metrics = array();
        $total = 0;
        $columns = $this->board->getColumns($project_id);

        foreach ($columns as $column) {

            $nb_tasks = $this->taskFinder->countByColumnId($project_id, $column['id']);
            $total += $nb_tasks;

            $metrics[] = array(
                'column_title' => $column['title'],
                'nb_tasks' => $nb_tasks,
            );
        }

        if ($total === 0) {
            return array();
        }

        foreach ($metrics as &$metric) {
            $metric['percentage'] = round(($metric['nb_tasks'] * 100) / $total, 2);
        }

        return $metrics;
    }

    /**
     * Get users repartition
     *
     * @access public
     * @param  integer   $project_id
     * @return array
     */
    public function getUserRepartition($project_id)
    {
        $metrics = array();
        $total = 0;
        $tasks = $this->taskFinder->getAll($project_id);
        $users = $this->projectPermission->getMemberList($project_id);

        foreach ($tasks as $task) {

            $user = isset($users[$task['owner_id']]) ? $users[$task['owner_id']] : $users[0];
            $total++;

            if (! isset($metrics[$user])) {
                $metrics[$user] = array(
                    'nb_tasks' => 0,
                    'percentage' => 0,
                    'user' => $user,
                );
            }

            $metrics[$user]['nb_tasks']++;
        }

        if ($total === 0) {
            return array();
        }

        foreach ($metrics as &$metric) {
            $metric['percentage'] = round(($metric['nb_tasks'] * 100) / $total, 2);
        }

        ksort($metrics);

        return array_values($metrics);
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
                $stats[$column_id]['count']++;
                $stats[$column_id]['time_spent'] += $time_spent;
            }
        }

        // Calculate average for each column
        foreach ($columns as $column_id => $column_title) {
            $stats[$column_id]['average'] = (int) ($stats[$column_id]['time_spent'] / $stats[$column_id]['count']);
        }

        return $stats;
    }
}
