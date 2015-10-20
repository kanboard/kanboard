<?php

namespace Kanboard\Model;

use PicoDb\Database;

/**
 * Project Daily Stats
 *
 * @package  model
 * @author   Frederic Guillot
 */
class ProjectDailyStats extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'project_daily_stats';

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
        $lead_cycle_time = $this->projectAnalytic->getAverageLeadAndCycleTime($project_id);

        return $this->db->transaction(function (Database $db) use ($project_id, $date, $lead_cycle_time) {

            // This call will fail if the record already exists
            // (cross database driver hack for INSERT..ON DUPLICATE KEY UPDATE)
            $db->table(ProjectDailyStats::TABLE)->insert(array(
                'day' => $date,
                'project_id' => $project_id,
                'avg_lead_time' => 0,
                'avg_cycle_time' => 0,
            ));

            $db->table(ProjectDailyStats::TABLE)
                ->eq('project_id', $project_id)
                ->eq('day', $date)
                ->update(array(
                    'avg_lead_time' => $lead_cycle_time['avg_lead_time'],
                    'avg_cycle_time' => $lead_cycle_time['avg_cycle_time'],
                ));
        });
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
        return $this->db->table(self::TABLE)
                        ->columns('day', 'avg_lead_time', 'avg_cycle_time')
                        ->eq(self::TABLE.'.project_id', $project_id)
                        ->gte('day', $from)
                        ->lte('day', $to)
                        ->asc(self::TABLE.'.day')
                        ->findAll();
    }
}
