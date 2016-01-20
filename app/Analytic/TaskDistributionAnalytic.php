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
    public function build($project_id, $swimlane_id = 0)
    {
        $metrics = array();
        $total = 0;
        $columns = $this->board->getColumns($project_id);

        foreach ($columns as $column) {
            if ($swimlane_id === 0) {
                $nb_tasks = $this->taskFinder->countByColumnId($project_id, $column['id']);
            }
			else
            {
                $nb_tasks = $this->taskFinder->countByColumnAndSwimlaneId($project_id, $column['id'], $swimlane_id);
            }

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
