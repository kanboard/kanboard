<?php

namespace Kanboard\Formatter;

use Kanboard\Core\Filter\FormatterInterface;

/**
 * Class TasksApiFormatter
 *
 * @package Kanboard\Formatter
 */
class TasksApiFormatter extends BaseFormatter implements FormatterInterface
{
    protected $tasks = array();

    public function withTasks($tasks)
    {
        $this->tasks = $tasks;
        return $this;
    }

    /**
     * Apply formatter
     *
     * @access public
     * @return mixed
     */
    public function format()
    {
        if (! empty($this->tasks)) {
            foreach ($this->tasks as &$task) {
                $task = $this->taskApiFormatter->withTask($task)->format();
            }
        }

        return $this->tasks;
    }
}
