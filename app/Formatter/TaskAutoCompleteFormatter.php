<?php

namespace Kanboard\Formatter;

use Kanboard\Core\Filter\FormatterInterface;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskModel;

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
        $tasks = $this->query->columns(
            TaskModel::TABLE.'.id',
            TaskModel::TABLE.'.title',
            ProjectModel::TABLE.'.name AS project_name'
        )->asc(TaskModel::TABLE.'.id')->findAll();

        foreach ($tasks as &$task) {
            $task['value'] = $task['title'];
            $task['label'] = $task['project_name'].' > #'.$task['id'].' '.$task['title'];
        }

        return $tasks;
    }
}
