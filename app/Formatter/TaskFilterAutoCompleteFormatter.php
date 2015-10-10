<?php

namespace Formatter;

use Model\Task;
use Model\TaskFilter;

/**
 * Autocomplete formatter for task filter
 *
 * @package  formatter
 * @author   Frederic Guillot
 */
class TaskFilterAutoCompleteFormatter extends TaskFilter implements FormatterInterface
{
    /**
     * Format the tasks for the ajax autocompletion
     *
     * @access public
     * @return array
     */
    public function format()
    {
        $tasks = $this->query->columns(Task::TABLE.'.id', Task::TABLE.'.title')->findAll();

        foreach ($tasks as &$task) {
            $task['value'] = $task['title'];
            $task['label'] = '#'.$task['id'].' - '.$task['title'];
        }

        return $tasks;
    }
}
