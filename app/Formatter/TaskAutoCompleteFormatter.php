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
    protected $limit = 25;

    /**
     * Limit number of results
     *
     * @param  $limit
     * @return $this
     */
    public function withLimit($limit)
    {
        $this->limit = $limit;
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
        $tasks = $this->query
            ->columns(
                TaskModel::TABLE.'.id',
                TaskModel::TABLE.'.title',
                ProjectModel::TABLE.'.name AS project_name'
            )
            ->asc(TaskModel::TABLE.'.id')
            ->limit($this->limit)
            ->findAll();

        foreach ($tasks as &$task) {
            $task['value'] = $task['title'];
            $task['label'] = $task['project_name'].' > #'.$task['id'].' '.$task['title'];
        }

        return $tasks;
    }
}
