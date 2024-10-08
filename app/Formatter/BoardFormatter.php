<?php

namespace Kanboard\Formatter;

use Kanboard\Core\Filter\FormatterInterface;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\SwimlaneModel;
use Kanboard\Model\TaskModel;

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
    public function withProjectId($projectId)
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
        $project = $this->projectModel->getById($this->projectId);
        $swimlanes = $this->swimlaneModel->getAllByStatus($this->projectId, SwimlaneModel::ACTIVE);
        $columns = $this->columnModel->getAllWithOpenedTaskCount($this->projectId);
        $task_count_by_swimlanes_and_columns = [];

        if ($project['per_swimlane_task_limits']) {
            foreach ($this->taskModel->getOpenTaskCountBySwimlaneAndColumn($this->projectId) as $task_count) {
                $task_count_by_swimlanes_and_columns[$task_count['swimlane_id']][$task_count['column_id']] = $task_count['nb_open_tasks'];
            }
        }

        if (empty($swimlanes) || empty($columns)) {
            return array();
        }

        $this->hook->reference('formatter:board:query', $this->query);

        $tasks = $this->query
            ->eq(TaskModel::TABLE.'.project_id', $this->projectId)
            ->asc(TaskModel::TABLE.'.position')
            ->findAll();

        $task_ids = array_column($tasks, 'id');
        $tags = $this->taskTagModel->getTagsByTaskIds($task_ids);

        return $this->boardSwimlaneFormatter
            ->withSwimlanes($swimlanes)
            ->withColumns($columns)
            ->withTasks($tasks)
            ->withTags($tags)
            ->withTaskCountBySwimlaneAndColumn($task_count_by_swimlanes_and_columns)
            ->format();
    }
}
