<?php

namespace Kanboard\Formatter;

use Kanboard\Core\Filter\FormatterInterface;

/**
 * Board Swimlane Formatter
 *
 * @package formatter
 * @author  Frederic Guillot
 */
class BoardSwimlaneFormatter extends BaseFormatter implements FormatterInterface
{
    protected $swimlanes = array();
    protected $columns = array();
    protected $tasks = array();
    protected $tags = array();

    /**
     * Set swimlanes
     *
     * @access public
     * @param  array $swimlanes
     * @return $this
     */
    public function withSwimlanes(array $swimlanes)
    {
        $this->swimlanes = $swimlanes;
        return $this;
    }

    /**
     * Set columns
     *
     * @access public
     * @param  array $columns
     * @return $this
     */
    public function withColumns(array $columns)
    {
        $this->columns = $columns;
        return $this;
    }

    /**
     * Set tasks
     *
     * @access public
     * @param  array $tasks
     * @return $this
     */
    public function withTasks(array $tasks)
    {
        $this->tasks = $tasks;
        return $this;
    }

    /**
     * Set tags
     *
     * @access public
     * @param  array $tags
     * @return $this
     */
    public function withTags(array $tags)
    {
        $this->tags = $tags;
        return $this;
    }

    /**
     * Apply formatter
     *
     * @access public
     * @return array
     */
    public function format()
    {
        $nb_swimlanes = count($this->swimlanes);

        foreach ($this->swimlanes as &$swimlane) {
            $columns = array_values(array_filter($this->columns, function($column) use ($swimlane) {
                return !array_key_exists('swimlane_id', $column) || $column['swimlane_id'] == $swimlane['id'];
            }));
            $nb_columns = count($columns);
            $swimlane['id'] = (int) $swimlane['id'];
            $swimlane['columns'] = $this->boardColumnFormatter
                ->withSwimlaneId($swimlane['id'])
                ->withColumns($columns)
                ->withTasks($this->tasks)
                ->withTags($this->tags)
                ->format();

            $swimlane['nb_swimlanes'] = $nb_swimlanes;
            $swimlane['nb_columns'] = $nb_columns;
            $swimlane['nb_tasks'] = array_column_sum($swimlane['columns'], 'nb_tasks');
            $swimlane['score'] = array_column_sum($swimlane['columns'], 'score');

            $this->calculateStatsByColumnAcrossSwimlanes($swimlane['columns']);

            foreach ($swimlane['columns'] as $columnIndex => &$column) {
                $column['column_nb_tasks'] = $this->swimlanes[0]['columns'][$columnIndex]['column_nb_tasks'];
                $column['column_nb_score'] = $this->swimlanes[0]['columns'][$columnIndex]['column_score'];
                // add number of open tasks to each column, ignoring the current filter
                $column['column_nb_open_tasks'] = $columns[array_search($column['id'], array_column($columns, 'id'))]['nb_open_tasks'];
            }
        }

        return $this->swimlanes;
    }

    /**
     * Calculate stats for each column across all swimlanes
     *
     * @access protected
     * @param  array $columns
     */
    protected function calculateStatsByColumnAcrossSwimlanes(array $columns)
    {
        foreach ($columns as $columnIndex => $column) {
            if (! isset($this->swimlanes[0]['columns'][$columnIndex]['column_nb_tasks'])) {
                $this->swimlanes[0]['columns'][$columnIndex]['column_nb_tasks'] = 0;
                $this->swimlanes[0]['columns'][$columnIndex]['column_score'] = 0;
            }

            $this->swimlanes[0]['columns'][$columnIndex]['column_nb_tasks'] += $column['nb_tasks'];
            $this->swimlanes[0]['columns'][$columnIndex]['column_score'] += $column['score'];
        }
    }
}
