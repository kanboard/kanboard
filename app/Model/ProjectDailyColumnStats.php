<?php

namespace Kanboard\Model;

use PicoDb\Database;

/**
 * Project Daily Column Stats
 *
 * @package  model
 * @author   Frederic Guillot
 */
class ProjectDailyColumnStats extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'project_daily_column_stats';

    /**
     * Update daily totals for the project and foreach column
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
        $status = $this->config->get('cfd_include_closed_tasks') == 1 ? array(Task::STATUS_OPEN, Task::STATUS_CLOSED) : array(Task::STATUS_OPEN);

        return $this->db->transaction(function (Database $db) use ($project_id, $date, $status) {

            $column_ids = $db->table(Board::TABLE)->eq('project_id', $project_id)->findAllByColumn('id');

            foreach ($column_ids as $column_id) {

                // This call will fail if the record already exists
                // (cross database driver hack for INSERT..ON DUPLICATE KEY UPDATE)
                $db->table(ProjectDailyColumnStats::TABLE)->insert(array(
                    'day' => $date,
                    'project_id' => $project_id,
                    'column_id' => $column_id,
                    'total' => 0,
                    'score' => 0,
                ));

                $db->table(ProjectDailyColumnStats::TABLE)
                    ->eq('project_id', $project_id)
                    ->eq('column_id', $column_id)
                    ->eq('day', $date)
                    ->update(array(
                        'score' => $db->table(Task::TABLE)
                                      ->eq('project_id', $project_id)
                                      ->eq('column_id', $column_id)
                                      ->eq('is_active', Task::STATUS_OPEN)
                                      ->sum('score'),
                        'total' => $db->table(Task::TABLE)
                                      ->eq('project_id', $project_id)
                                      ->eq('column_id', $column_id)
                                      ->in('is_active', $status)
                                      ->count()
                    ));
            }
        });
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
        $rq = $this->db->execute(
            'SELECT COUNT(DISTINCT day) FROM '.self::TABLE.' WHERE day >= ? AND day <= ? AND project_id=?',
            array($from, $to, $project_id)
        );

        return $rq !== false ? $rq->fetchColumn(0) : 0;
    }

    /**
     * Get raw metrics for the project within a data range
     *
     * @access public
     * @param  integer    $project_id    Project id
     * @param  string     $from          Start date (ISO format YYYY-MM-DD)
     * @param  string     $to            End date
     * @return array
     */
    public function getRawMetrics($project_id, $from, $to)
    {
        return $this->db->table(ProjectDailyColumnStats::TABLE)
                        ->columns(
                            ProjectDailyColumnStats::TABLE.'.column_id',
                            ProjectDailyColumnStats::TABLE.'.day',
                            ProjectDailyColumnStats::TABLE.'.total',
                            ProjectDailyColumnStats::TABLE.'.score',
                            Board::TABLE.'.title AS column_title'
                        )
                        ->join(Board::TABLE, 'id', 'column_id')
                        ->eq(ProjectDailyColumnStats::TABLE.'.project_id', $project_id)
                        ->gte('day', $from)
                        ->lte('day', $to)
                        ->asc(ProjectDailyColumnStats::TABLE.'.day')
                        ->findAll();
    }

    /**
     * Get raw metrics for the project within a data range grouped by day
     *
     * @access public
     * @param  integer    $project_id    Project id
     * @param  string     $from          Start date (ISO format YYYY-MM-DD)
     * @param  string     $to            End date
     * @return array
     */
    public function getRawMetricsByDay($project_id, $from, $to)
    {
        return $this->db->table(ProjectDailyColumnStats::TABLE)
                        ->columns(
                            ProjectDailyColumnStats::TABLE.'.day',
                            'SUM('.ProjectDailyColumnStats::TABLE.'.total) AS total',
                            'SUM('.ProjectDailyColumnStats::TABLE.'.score) AS score'
                        )
                        ->eq(ProjectDailyColumnStats::TABLE.'.project_id', $project_id)
                        ->gte('day', $from)
                        ->lte('day', $to)
                        ->asc(ProjectDailyColumnStats::TABLE.'.day')
                        ->groupBy(ProjectDailyColumnStats::TABLE.'.day')
                        ->findAll();
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
     * @param  string     $column        Column to aggregate
     * @return array
     */
    public function getAggregatedMetrics($project_id, $from, $to, $column = 'total')
    {
        $columns = $this->board->getColumnsList($project_id);
        $column_ids = array_keys($columns);
        $metrics = array(array_merge(array(e('Date')), array_values($columns)));
        $aggregates = array();

        // Fetch metrics for the project
        $records = $this->db->table(ProjectDailyColumnStats::TABLE)
                            ->eq('project_id', $project_id)
                            ->gte('day', $from)
                            ->lte('day', $to)
                            ->findAll();

        // Aggregate by day
        foreach ($records as $record) {
            if (! isset($aggregates[$record['day']])) {
                $aggregates[$record['day']] = array($record['day']);
            }

            $aggregates[$record['day']][$record['column_id']] = $record[$column];
        }

        // Aggregate by row
        foreach ($aggregates as $aggregate) {
            $row = array($aggregate[0]);

            foreach ($column_ids as $column_id) {
                $row[] = (int) $aggregate[$column_id];
            }

            $metrics[] = $row;
        }

        return $metrics;
    }
}
