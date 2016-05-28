<?php

namespace Kanboard\Model;

use Kanboard\Core\Base;

/**
 * Project Daily Column Stats
 *
 * @package  Kanboard\Model
 * @author   Frederic Guillot
 */
class ProjectDailyColumnStatsModel extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'project_daily_column_stats';

    /**
     * Update daily totals for the project and for each column
     *
     * "total" is the number open of tasks in the column
     * "score" is the sum of tasks score in the column
     *
     * @access public
     * @param  integer    $project_id    Project id
     * @param  string     $date          Record date (YYYY-MM-DD)
     * @return boolean
     */
    public function updateTotals($project_id, $date)
    {
        $this->db->startTransaction();
        $this->db->table(self::TABLE)->eq('project_id', $project_id)->eq('day', $date)->remove();

        foreach ($this->getStatsByColumns($project_id) as $column_id => $column) {
            $this->db->table(self::TABLE)->insert(array(
                'day' => $date,
                'project_id' => $project_id,
                'column_id' => $column_id,
                'total' => $column['total'],
                'score' => $column['score'],
            ));
        }

        $this->db->closeTransaction();

        return true;
    }

    /**
     * Count the number of recorded days for the data range
     *
     * @access public
     * @param  integer    $project_id    Project id
     * @param  string     $from          Start date (ISO format YYYY-MM-DD)
     * @param  string     $to            End date
     * @return integer
     */
    public function countDays($project_id, $from, $to)
    {
        return $this->db->table(self::TABLE)
            ->eq('project_id', $project_id)
            ->gte('day', $from)
            ->lte('day', $to)
            ->findOneColumn('COUNT(DISTINCT day)');
    }

    /**
     * Get aggregated metrics for the project within a data range
     *
     * [
     *    ['Date', 'Column1', 'Column2'],
     *    ['2014-11-16', 2, 5],
     *    ['2014-11-17', 20, 15],
     * ]
     *
     * @access public
     * @param  integer    $project_id    Project id
     * @param  string     $from          Start date (ISO format YYYY-MM-DD)
     * @param  string     $to            End date
     * @param  string     $field         Column to aggregate
     * @return array
     */
    public function getAggregatedMetrics($project_id, $from, $to, $field = 'total')
    {
        $columns = $this->columnModel->getList($project_id);
        $metrics = $this->getMetrics($project_id, $from, $to);
        return $this->buildAggregate($metrics, $columns, $field);
    }

    /**
     * Fetch metrics
     *
     * @access public
     * @param  integer    $project_id    Project id
     * @param  string     $from          Start date (ISO format YYYY-MM-DD)
     * @param  string     $to            End date
     * @return array
     */
    public function getMetrics($project_id, $from, $to)
    {
        return $this->db->table(self::TABLE)
            ->eq('project_id', $project_id)
            ->gte('day', $from)
            ->lte('day', $to)
            ->asc(self::TABLE.'.day')
            ->findAll();
    }

    /**
     * Build aggregate
     *
     * @access private
     * @param  array   $metrics
     * @param  array   $columns
     * @param  string  $field
     * @return array
     */
    private function buildAggregate(array &$metrics, array &$columns, $field)
    {
        $column_ids = array_keys($columns);
        $days = array_unique(array_column($metrics, 'day'));
        $rows = array(array_merge(array(e('Date')), array_values($columns)));

        foreach ($days as $day) {
            $rows[] = $this->buildRowAggregate($metrics, $column_ids, $day, $field);
        }

        return $rows;
    }

    /**
     * Build one row of the aggregate
     *
     * @access private
     * @param  array   $metrics
     * @param  array   $column_ids
     * @param  string  $day
     * @param  string  $field
     * @return array
     */
    private function buildRowAggregate(array &$metrics, array &$column_ids, $day, $field)
    {
        $row = array($day);

        foreach ($column_ids as $column_id) {
            $row[] = $this->findValueInMetrics($metrics, $day, $column_id, $field);
        }

        return $row;
    }

    /**
     * Find the value in the metrics
     *
     * @access private
     * @param  array   $metrics
     * @param  string  $day
     * @param  string  $column_id
     * @param  string  $field
     * @return integer
     */
    private function findValueInMetrics(array &$metrics, $day, $column_id, $field)
    {
        foreach ($metrics as $metric) {
            if ($metric['day'] === $day && $metric['column_id'] == $column_id) {
                return (int) $metric[$field];
            }
        }

        return 0;
    }

    /**
     * Get number of tasks and score by columns
     *
     * @access private
     * @param  integer $project_id
     * @return array
     */
    private function getStatsByColumns($project_id)
    {
        $totals = $this->getTotalByColumns($project_id);
        $scores = $this->getScoreByColumns($project_id);
        $columns = array();

        foreach ($totals as $column_id => $total) {
            $columns[$column_id] = array('total' => $total, 'score' => 0);
        }

        foreach ($scores as $column_id => $score) {
            $columns[$column_id]['score'] = (int) $score;
        }

        return $columns;
    }

    /**
     * Get number of tasks and score by columns
     *
     * @access private
     * @param  integer $project_id
     * @return array
     */
    private function getScoreByColumns($project_id)
    {
        $stats = $this->db->table(TaskModel::TABLE)
            ->columns('column_id', 'SUM(score) AS score')
            ->eq('project_id', $project_id)
            ->eq('is_active', TaskModel::STATUS_OPEN)
            ->notNull('score')
            ->groupBy('column_id')
            ->findAll();

        return array_column($stats, 'score', 'column_id');
    }

    /**
     * Get number of tasks and score by columns
     *
     * @access private
     * @param  integer $project_id
     * @return array
     */
    private function getTotalByColumns($project_id)
    {
        $stats = $this->db->table(TaskModel::TABLE)
            ->columns('column_id', 'COUNT(*) AS total')
            ->eq('project_id', $project_id)
            ->in('is_active', $this->getTaskStatusConfig())
            ->groupBy('column_id')
            ->findAll();

        return array_column($stats, 'total', 'column_id');
    }

    /**
     * Get task status to use for total calculation
     *
     * @access private
     * @return array
     */
    private function getTaskStatusConfig()
    {
        if ($this->configModel->get('cfd_include_closed_tasks') == 1) {
            return array(TaskModel::STATUS_OPEN, TaskModel::STATUS_CLOSED);
        }

        return array(TaskModel::STATUS_OPEN);
    }
}
