<?php

namespace Model;

/**
 * Project Paginator
 *
 * @package  model
 * @author   Frederic Guillot
 */
class ProjectPaginator extends Base
{
    /**
     * Get project summary for a list of project (number of tasks for each column)
     *
     * @access public
     * @param  array      $project_ids     List of project id
     * @param  integer    $offset          Offset
     * @param  integer    $limit           Limit
     * @param  string     $column          Sorting column
     * @param  string     $direction       Sorting direction
     * @return array
     */
    public function projectSummaries(array $project_ids, $offset = 0, $limit = 25, $column = 'name', $direction = 'asc')
    {
        if (empty($project_ids)) {
            return array();
        }

        $projects = $this->db
                         ->table(Project::TABLE)
                         ->in('id', $project_ids)
                         ->offset($offset)
                         ->limit($limit)
                         ->orderBy($column, $direction)
                         ->findAll();

        foreach ($projects as &$project) {

            $project['columns'] = $this->board->getColumns($project['id']);
            $stats = $this->board->getColumnStats($project['id']);

            foreach ($project['columns'] as &$column) {
                $column['nb_tasks'] = isset($stats[$column['id']]) ? $stats[$column['id']] : 0;
            }
        }

        return $projects;
    }
}
