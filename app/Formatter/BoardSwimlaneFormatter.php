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
    protected $taskCountBySwimlaneAndColumn = array();

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
     * Set task count by swimlane and column
     *
     * @access public
     * @param  array $taskCountBySwimlaneAndColumn
     * @return $this
     */
    public function withTaskCountBySwimlaneAndColumn(array $taskCountBySwimlaneAndColumn)
    {
        $this->taskCountBySwimlaneAndColumn = $taskCountBySwimlaneAndColumn;
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
        $nb_columns = count($this->columns);
        $tasks_stats_by_column_across_swimlanes = [];

        foreach ($this->columns as $column) {
            $tasks_stats_by_column_across_swimlanes[$column['id']] = [
                'nb_visible_tasks_across_swimlane' => 0,
                'nb_unfiltered_tasks_across_swimlane' => 0,
                'cumulative_score_across_swimlane' => 0,
            ];
        }

        foreach ($this->swimlanes as &$swimlane) {
            $swimlane['id'] = (int) $swimlane['id'];
            $swimlane['columns'] = $this->boardColumnFormatter
                ->withSwimlaneId($swimlane['id'])
                ->withColumns($this->columns)
                ->withTasks($this->tasks)
                ->withTags($this->tags)
                ->withTaskCountBySwimlaneAndColumn($this->taskCountBySwimlaneAndColumn)
                ->format();

            $swimlane['nb_swimlanes'] = $nb_swimlanes;
            $swimlane['nb_columns'] = $nb_columns;
            $swimlane['nb_tasks'] = array_column_sum($swimlane['columns'], 'nb_tasks');
            $swimlane['score'] = array_column_sum($swimlane['columns'], 'score');

            foreach ($swimlane['columns'] as &$column) {
                $tasks_stats_by_column_across_swimlanes[$column['id']]['nb_visible_tasks_across_swimlane'] += count($column['tasks']);
                $tasks_stats_by_column_across_swimlanes[$column['id']]['nb_unfiltered_tasks_across_swimlane'] = $column['nb_open_tasks'];
                $tasks_stats_by_column_across_swimlanes[$column['id']]['cumulative_score_across_swimlane'] += $column['score'];
            }
        }

        foreach ($this->swimlanes as &$swimlane) {
            foreach ($swimlane['columns'] as &$column) {
                $column['nb_visible_tasks_across_swimlane'] = $tasks_stats_by_column_across_swimlanes[$column['id']]['nb_visible_tasks_across_swimlane'];
                $column['nb_unfiltered_tasks_across_swimlane'] = $tasks_stats_by_column_across_swimlanes[$column['id']]['nb_unfiltered_tasks_across_swimlane'];
                $column['cumulative_score_across_swimlane'] = $tasks_stats_by_column_across_swimlanes[$column['id']]['cumulative_score_across_swimlane'];
            }
        }

        return $this->swimlanes;
    }
}
