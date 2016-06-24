<?php

namespace Kanboard\Formatter;

use Kanboard\Core\Filter\FormatterInterface;

/**
 * Board Task Formatter
 *
 * @package formatter
 * @author  Frederic Guillot
 */
class BoardTaskFormatter extends BaseFormatter implements FormatterInterface
{
    protected $tasks = array();
    protected $columnId = 0;
    protected $swimlaneId = 0;

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
     * Set columnId
     *
     * @access public
     * @param  integer $columnId
     * @return $this
     */
    public function withColumnId($columnId)
    {
        $this->columnId = $columnId;
        return $this;
    }

    /**
     * Set swimlaneId
     *
     * @access public
     * @param  integer $swimlaneId
     * @return $this
     */
    public function withSwimlaneId($swimlaneId)
    {
        $this->swimlaneId = $swimlaneId;
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
        return array_values(array_filter($this->tasks, array($this, 'filterTasks')));
    }

    /**
     * Keep only tasks of the given column and swimlane
     *
     * @access public
     * @param  array $task
     * @return bool
     */
    public function filterTasks(array $task)
    {
        return $task['column_id'] == $this->columnId && $task['swimlane_id'] == $this->swimlaneId;
    }
}
