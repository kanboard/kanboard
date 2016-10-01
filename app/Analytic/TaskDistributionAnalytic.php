<?php

namespace Kanboard\Analytic;

use Kanboard\Core\Base;

/**
 * Task Distribution
 *
 * @package  analytic
 * @author   Frederic Guillot
 */
class TaskDistributionAnalytic extends Base
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
        $metrics = array();
        $total = 0;
        $columns = $this->columnModel->getAll($project_id);

        foreach ($columns as $column) {
            $nb_tasks = $this->taskFinderModel->countByColumnId($project_id, $column['id']);
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
}
