<?php

namespace Kanboard\Export;

use Kanboard\Core\Base;
use Kanboard\Model\CategoryModel;
use Kanboard\Model\ColumnModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\SwimlaneModel;
use Kanboard\Model\TaskModel;
use Kanboard\Model\UserModel;

/**
 * Task Export
 *
 * @package  export
 * @author   Frederic Guillot
 */
class TaskExport extends Base
{
    /**
     * Fetch tasks and return the prepared CSV
     *
     * @access public
     * @param  integer $project_id Project id
     * @param  mixed   $from       Start date (timestamp or user formatted date)
     * @param  mixed   $to         End date (timestamp or user formatted date)
     * @return array
     */
    public function export($project_id, $from, $to)
    {
        $tasks = $this->getTasks($project_id, $from, $to);
        $taskIds = array_column($tasks, 'id');
        $tags = $this->taskTagModel->getTagsByTaskIds($taskIds);
        $colors = $this->colorModel->getList();
        $results = array($this->getColumns());

        foreach ($tasks as &$task) {
            $task = $this->format($task, $colors, $tags);
            $results[] = array_values($task);
        }

        return $results;
    }

    /**
     * Get the list of tasks for a given project and date range
     *
     * @access protected
     * @param  integer $project_id Project id
     * @param  mixed   $from       Start date (timestamp or user formatted date)
     * @param  mixed   $to         End date (timestamp or user formatted date)
     * @return array
     */
    protected function getTasks($project_id, $from, $to)
    {
        if (!is_numeric($from)) {
            $from = $this->dateParser->removeTimeFromTimestamp($this->dateParser->getTimestamp($from));
        }

        if (!is_numeric($to)) {
            $to = $this->dateParser->removeTimeFromTimestamp(strtotime('+1 day', $this->dateParser->getTimestamp($to)));
        }

        return $this->db->table(TaskModel::TABLE)
            ->columns(
                TaskModel::TABLE . '.id',
                TaskModel::TABLE . '.reference',
                ProjectModel::TABLE . '.name AS project_name',
                TaskModel::TABLE . '.is_active',
                CategoryModel::TABLE . '.name AS category_name',
                SwimlaneModel::TABLE . '.name AS swimlane_name',
                ColumnModel::TABLE . '.title AS column_title',
                TaskModel::TABLE . '.position',
                TaskModel::TABLE . '.color_id',
                TaskModel::TABLE . '.date_due',
                'uc.username AS creator_username',
                'uc.name AS creator_name',
                UserModel::TABLE . '.username AS assignee_username',
                UserModel::TABLE . '.name AS assignee_name',
                TaskModel::TABLE . '.score',
                TaskModel::TABLE . '.title',
                TaskModel::TABLE . '.date_creation',
                TaskModel::TABLE . '.date_modification',
                TaskModel::TABLE . '.date_completed',
                TaskModel::TABLE . '.date_started',
                TaskModel::TABLE . '.time_estimated',
                TaskModel::TABLE . '.time_spent',
                TaskModel::TABLE . '.priority'
            )
            ->join(UserModel::TABLE, 'id', 'owner_id', TaskModel::TABLE)
            ->left(UserModel::TABLE, 'uc', 'id', TaskModel::TABLE, 'creator_id')
            ->join(CategoryModel::TABLE, 'id', 'category_id', TaskModel::TABLE)
            ->join(ColumnModel::TABLE, 'id', 'column_id', TaskModel::TABLE)
            ->join(SwimlaneModel::TABLE, 'id', 'swimlane_id', TaskModel::TABLE)
            ->join(ProjectModel::TABLE, 'id', 'project_id', TaskModel::TABLE)
            ->gte(TaskModel::TABLE . '.date_creation', $from)
            ->lte(TaskModel::TABLE . '.date_creation', $to)
            ->eq(TaskModel::TABLE . '.project_id', $project_id)
            ->asc(TaskModel::TABLE.'.id')
            ->findAll();
    }

    /**
     * Format the output of a task array
     *
     * @access protected
     * @param  array  $task
     * @param  array  $colors
     * @param  array  $tags
     * @return array
     */
    protected function format(array &$task, array $colors, array &$tags)
    {
        $task['is_active'] = $task['is_active'] == TaskModel::STATUS_OPEN ? e('Open') : e('Closed');
        $task['color_id'] = $colors[$task['color_id']];
        $task['score'] = $task['score'] ?: 0;
        $task['tags'] = '';

        $task = $this->dateParser->format(
            $task,
            array('date_due', 'date_modification', 'date_creation', 'date_started', 'date_completed'),
            $this->dateParser->getUserDateTimeFormat()
        );

        if (isset($tags[$task['id']])) {
            $taskTags = array_column($tags[$task['id']], 'name');
            $task['tags'] = implode(', ', $taskTags);
        }

        return $task;
    }

    /**
     * Get column titles
     *
     * @access protected
     * @return string[]
     */
    protected function getColumns()
    {
        return array(
            e('Task Id'),
            e('Reference'),
            e('Project'),
            e('Status'),
            e('Category'),
            e('Swimlane'),
            e('Column'),
            e('Position'),
            e('Color'),
            e('Due date'),
            e('Creator'),
            e('Creator Name'),
            e('Assignee Username'),
            e('Assignee Name'),
            e('Complexity'),
            e('Title'),
            e('Creation date'),
            e('Modification date'),
            e('Completion date'),
            e('Start date'),
            e('Time estimated'),
            e('Time spent'),
            e('Priority'),
            e('Tags'),
        );
    }
}
