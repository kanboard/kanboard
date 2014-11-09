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
     * Get task repartition
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

        foreach ($metrics as &$metric) {
            $metric['percentage'] = round(($metric['nb_tasks'] * 100) / $total, 2);
        }

        return $metrics;
    }
}
