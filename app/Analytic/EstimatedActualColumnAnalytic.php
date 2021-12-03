<?php

namespace Kanboard\Analytic;

use Kanboard\Core\Base;
use Kanboard\Model\TaskModel;

/**
 * Estimated vs actual time per column
 *
 * @package  analytic
 * @author   Frederic Guillot
 */
class EstimatedActualColumnAnalytic extends Base
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
        $rows = $this->db->table(TaskModel::TABLE)
            ->columns('SUM(time_estimated) AS hours_estimated', 'SUM(time_spent) AS hours_spent', 'column_id')
            ->eq('project_id', $project_id)
            ->groupBy('column_id')
            ->findAll();

        $columns = $this->columnModel->getList($project_id);

        $metrics = [];
        foreach ($columns as $column_id => $column_title) {
            $metrics[$column_id] = array(
                'hours_spent' => 0,
                'hours_estimated' => 0,
                'title' => $column_title,
            );
        }

        foreach ($rows as $row) {
            $metrics[$row['column_id']]['hours_spent'] = (float) $row['hours_spent'];
            $metrics[$row['column_id']]['hours_estimated'] = (float) $row['hours_estimated'];
        }

        return $metrics;
    }
}
