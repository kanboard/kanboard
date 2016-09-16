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
    protected $tags = array();
    protected $columnId = 0;
    protected $swimlaneId = 0;

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
        $tasks = array_values(array_filter($this->tasks, array($this, 'filterTasks')));
        array_merge_relation($tasks, $this->tags, 'tags', 'id');

        foreach ($tasks as &$task) {
            $task['is_draggable'] = $this->helper->projectRole->isDraggable($task);
        }

        return $tasks;
    }

    /**
     * Keep only tasks of the given column and swimlane
     *
     * @access protected
     * @param  array $task
     * @return bool
     */
    protected function filterTasks(array $task)
    {
        return $task['column_id'] == $this->columnId && $task['swimlane_id'] == $this->swimlaneId;
    }
}
