<?php

namespace Kanboard\Formatter;

use Kanboard\Core\Filter\FormatterInterface;
use Kanboard\Model\Task;

/**
 * Board Formatter
 *
 * @package formatter
 * @author  Frederic Guillot
 */
class BoardFormatter extends BaseFormatter implements FormatterInterface
{
    /**
     * Project id
     *
     * @access protected
     * @var integer
     */
    protected $projectId;

    /**
     * Set ProjectId
     *
     * @access public
     * @param  integer $projectId
     * @return $this
     */
    public function setProjectId($projectId)
    {
        $this->projectId = $projectId;
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
            ->eq(Task::TABLE.'.project_id', $this->projectId)
            ->asc(Task::TABLE.'.position')
            ->findAll();

        return $this->board->getBoard($this->projectId, function ($project_id, $column_id, $swimlane_id) use ($tasks) {
            return array_filter($tasks, function (array $task) use ($column_id, $swimlane_id) {
                return $task['column_id'] == $column_id && $task['swimlane_id'] == $swimlane_id;
            });
        });
    }
}
