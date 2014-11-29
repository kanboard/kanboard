<?php

namespace Model;

use Core\Template;
use Event\ProjectDailySummaryListener;

/**
 * Project daily summary
 *
 * @package  model
 * @author   Frederic Guillot
 */
class ProjectDailySummary extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'project_daily_summaries';

    /**
     * Update daily totals for the project
     *
     * @access public
     * @param  integer    $project_id    Project id
     * @param  string     $date          Record date (YYYY-MM-DD)
     * @return boolean
     */
    public function updateTotals($project_id, $date)
    {
        return $this->db->transaction(function($db) use ($project_id, $date) {

            $column_ids = $db->table(Board::TABLE)->eq('project_id', $project_id)->findAllByColumn('id');

            foreach ($column_ids as $column_id) {

                // This call will fail if the record already exists
                // (cross database driver hack for INSERT..ON DUPLICATE KEY UPDATE)
                $db->table(ProjectDailySummary::TABLE)->insert(array(
                    'day' => $date,
                    'project_id' => $project_id,
                    'column_id' => $column_id,
                    'total' => 0,
                ));

                $db->table(ProjectDailySummary::TABLE)
                    ->eq('project_id', $project_id)
                    ->eq('column_id', $column_id)
                    ->eq('day', $date)
                    ->update(array(
                        'total' => $db->table(Task::TABLE)
                                      ->eq('project_id', $project_id)
                                      ->eq('column_id', $column_id)
                                      ->eq('is_active', Task::STATUS_OPEN)
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
        return $this->db->table(ProjectDailySummary::TABLE)
                        ->columns(
                            ProjectDailySummary::TABLE.'.column_id',
                            ProjectDailySummary::TABLE.'.day',
                            ProjectDailySummary::TABLE.'.total',
                            Board::TABLE.'.title AS column_title'
                        )
                        ->join(Board::TABLE, 'id', 'column_id')
                        ->eq(ProjectDailySummary::TABLE.'.project_id', $project_id)
                        ->gte('day', $from)
                        ->lte('day', $to)
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
     * @return array
     */
    public function getAggregatedMetrics($project_id, $from, $to)
    {
        $columns = $this->board->getColumnsList($project_id);
        $column_ids = array_keys($columns);
        $metrics = array(array(e('Date')) + $columns);
        $aggregates = array();

        // Fetch metrics for the project
        $records = $this->db->table(ProjectDailySummary::TABLE)
                            ->eq('project_id', $project_id)
                            ->gte('day', $from)
                            ->lte('day', $to)
                            ->findAll();

        // Aggregate by day
        foreach ($records as $record) {

            if (! isset($aggregates[$record['day']])) {
                $aggregates[$record['day']] = array($record['day']);
            }

            $aggregates[$record['day']][$record['column_id']] = $record['total'];
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

    /**
     * Attach events to be able to record the metrics
     *
     * @access public
     */
    public function attachEvents()
    {
        $events = array(
            Task::EVENT_CREATE,
            Task::EVENT_CLOSE,
            Task::EVENT_OPEN,
            Task::EVENT_MOVE_COLUMN,
        );

        $listener = new ProjectDailySummaryListener($this->container);

        foreach ($events as $event_name) {
            $this->event->attach($event_name, $listener);
        }
    }
}
