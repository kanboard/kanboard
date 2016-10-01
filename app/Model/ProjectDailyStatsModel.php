<?php

namespace Kanboard\Model;

use Kanboard\Core\Base;

/**
 * Project Daily Stats
 *
 * @package  Kanboard\Model
 * @author   Frederic Guillot
 */
class ProjectDailyStatsModel extends Base
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
        $this->db->startTransaction();

        $lead_cycle_time = $this->averageLeadCycleTimeAnalytic->build($project_id);

        $this->db->table(self::TABLE)->eq('day', $date)->eq('project_id', $project_id)->remove();

        $this->db->table(self::TABLE)->insert(array(
            'day' => $date,
            'project_id' => $project_id,
            'avg_lead_time' => $lead_cycle_time['avg_lead_time'],
            'avg_cycle_time' => $lead_cycle_time['avg_cycle_time'],
        ));

        $this->db->closeTransaction();

        return true;
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
        $metrics = $this->db->table(self::TABLE)
            ->columns('day', 'avg_lead_time', 'avg_cycle_time')
            ->eq('project_id', $project_id)
            ->gte('day', $from)
            ->lte('day', $to)
            ->asc('day')
            ->findAll();

        foreach ($metrics as &$metric) {
            $metric['avg_lead_time'] = (int) $metric['avg_lead_time'];
            $metric['avg_cycle_time'] = (int) $metric['avg_cycle_time'];
        }

        return $metrics;
    }
}
