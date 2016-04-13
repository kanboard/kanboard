<?php

namespace Kanboard\Formatter;

use Kanboard\Core\Filter\FormatterInterface;
use Kanboard\Model\Task;

/**
 * Task AutoComplete Formatter
 *
 * @package formatter
 * @author  Frederic Guillot
 */
class TaskAutoCompleteFormatter extends BaseFormatter implements FormatterInterface
{
    /**
     * Apply formatter
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
