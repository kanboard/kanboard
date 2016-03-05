<?php

namespace Kanboard\Model;

/**
 * Board model
 *
 * @package  model
 * @author   Frederic Guillot
 */
class Board extends Base
{
    /**
     * Get Kanboard default columns
     *
     * @access public
     * @return string[]
     */
    public function getDefaultColumns()
    {
        return array(t('Backlog'), t('Ready'), t('Work in progress'), t('Done'));
    }

    /**
     * Get user default columns
     *
     * @access public
     * @return array
     */
    public function getUserColumns()
    {
        $column_names = explode(',', $this->config->get('board_columns', implode(',', $this->getDefaultColumns())));
        $columns = array();

        foreach ($column_names as $column_name) {
            $column_name = trim($column_name);

            if (! empty($column_name)) {
                $columns[] = array('title' => $column_name, 'task_limit' => 0, 'description' => '');
            }
        }

        return $columns;
    }

    /**
     * Create a board with default columns, must be executed inside a transaction
     *
     * @access public
     * @param  integer  $project_id   Project id
     * @param  array    $columns      Column parameters [ 'title' => 'boo', 'task_limit' => 2 ... ]
     * @return boolean
     */
    public function create($project_id, array $columns)
    {
        $position = 0;

        foreach ($columns as $column) {
            $values = array(
                'title' => $column['title'],
                'position' => ++$position,
                'project_id' => $project_id,
                'task_limit' => $column['task_limit'],
                'description' => $column['description'],
            );

            if (! $this->db->table(Column::TABLE)->save($values)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Copy board columns from a project to another one
     *
     * @author Antonio Rabelo
     * @param  integer    $project_from      Project Template
     * @param  integer    $project_to        Project that receives the copy
     * @return boolean
     */
    public function duplicate($project_from, $project_to)
    {
        $columns = $this->db->table(Column::TABLE)
                            ->columns('title', 'task_limit', 'description')
                            ->eq('project_id', $project_from)
                            ->asc('position')
                            ->findAll();

        return $this->board->create($project_to, $columns);
    }

    /**
     * Get all tasks sorted by columns and swimlanes
     *
     * @access public
     * @param  integer  $project_id
     * @param  callable $callback
     * @return array
     */
    public function getBoard($project_id, $callback = null)
    {
        $swimlanes = $this->swimlane->getSwimlanes($project_id);
        $columns = $this->column->getAll($project_id);
        $nb_columns = count($columns);

        for ($i = 0, $ilen = count($swimlanes); $i < $ilen; $i++) {
            $swimlanes[$i]['columns'] = $columns;
            $swimlanes[$i]['nb_columns'] = $nb_columns;
            $swimlanes[$i]['nb_tasks'] = 0;
            $swimlanes[$i]['nb_swimlanes'] = $ilen;

            for ($j = 0; $j < $nb_columns; $j++) {
                $column_id = $columns[$j]['id'];
                $swimlane_id = $swimlanes[$i]['id'];

                if (! isset($swimlanes[0]['columns'][$j]['nb_column_tasks'])) {
                    $swimlanes[0]['columns'][$j]['nb_column_tasks'] = 0;
                    $swimlanes[0]['columns'][$j]['total_score'] = 0;
                }

                $swimlanes[$i]['columns'][$j]['tasks'] = $callback === null ? $this->taskFinder->getTasksByColumnAndSwimlane($project_id, $column_id, $swimlane_id) : $callback($project_id, $column_id, $swimlane_id);
                $swimlanes[$i]['columns'][$j]['nb_tasks'] = count($swimlanes[$i]['columns'][$j]['tasks']);
                $swimlanes[$i]['columns'][$j]['score'] = $this->getColumnSum($swimlanes[$i]['columns'][$j]['tasks'], 'score');
                $swimlanes[$i]['nb_tasks'] += $swimlanes[$i]['columns'][$j]['nb_tasks'];
                $swimlanes[0]['columns'][$j]['nb_column_tasks'] += $swimlanes[$i]['columns'][$j]['nb_tasks'];
                $swimlanes[0]['columns'][$j]['total_score'] += $swimlanes[$i]['columns'][$j]['score'];
            }
        }

        return $swimlanes;
    }

    /**
     * Calculate the sum of the defined field for a list of tasks
     *
     * @access public
     * @param  array   $tasks
     * @param  string  $field
     * @return integer
     */
    public function getColumnSum(array &$tasks, $field)
    {
        $sum = 0;

        foreach ($tasks as $task) {
            $sum += $task[$field];
        }

        return $sum;
    }

    /**
     * Get the total of tasks per column
     *
     * @access public
     * @param  integer   $project_id
     * @param  boolean   $prepend       Prepend default value
     * @return array
     */
    public function getColumnStats($project_id, $prepend = false)
    {
        $listing = $this->db
                        ->hashtable(Task::TABLE)
                        ->eq('project_id', $project_id)
                        ->eq('is_active', 1)
                        ->groupBy('column_id')
                        ->getAll('column_id', 'COUNT(*) AS total');

        return $prepend ? array(-1 => t('All columns')) + $listing : $listing;
    }
}
