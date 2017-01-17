<?php

namespace Kanboard\Formatter;

use Kanboard\Core\Filter\FormatterInterface;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskModel;

/**
 * Class TaskSuggestMenuFormatter
 *
 * @package Kanboard\Formatter
 * @author  Frederic Guillot
 */
class TaskSuggestMenuFormatter extends BaseFormatter implements FormatterInterface
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
     * @return mixed
     */
    public function format()
    {
        $result = array();
        $tasks = $this->query
            ->columns(
                TaskModel::TABLE.'.id',
                TaskModel::TABLE.'.title',
                ProjectModel::TABLE.'.name AS project_name'
            )
            ->asc(TaskModel::TABLE.'.id')
            ->limit($this->limit)
            ->findAll();

        foreach ($tasks as $task) {
            $html = '#'.$task['id'].' ';
            $html .= $this->helper->text->e($task['title']).' ';
            $html .= '<small>'.$this->helper->text->e($task['project_name']).'</small>';

            $result[] = array(
                'value' => (string) $task['id'],
                'html' => $html,
            );
        }

        return $result;
    }
}
