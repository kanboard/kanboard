<?php

namespace Kanboard\Model;

use Kanboard\Core\Base;

/**
 * Task Duplication
 *
 * @package  Kanboard\Model
 * @author   Frederic Guillot
 */
class TaskDuplicationModel extends Base
{
    /**
     * Fields to copy when duplicating a task
     *
     * @access protected
     * @var string[]
     */
    protected $fieldsToDuplicate = array(
        'title',
        'description',
        'date_due',
        'color_id',
        'project_id',
        'column_id',
        'owner_id',
        'score',
        'priority',
        'category_id',
        'time_estimated',
        'swimlane_id',
        'recurrence_status',
        'recurrence_trigger',
        'recurrence_factor',
        'recurrence_timeframe',
        'recurrence_basedate',
    );

    /**
     * Duplicate a task to the same project
     *
     * @access public
     * @param  integer             $task_id      Task id
     * @return boolean|integer                   Duplicated task id
     */
    public function duplicate($task_id)
    {
        $values = $this->copyFields($task_id);
        $values['title'] = t('[DUPLICATE]').' '.$values['title'];

        $new_task_id = $this->save($task_id, $values);

        if ($new_task_id !== false) {
            $this->tagDuplicationModel->duplicateTaskTags($task_id, $new_task_id);
        }

        return $new_task_id;
    }

    /**
     * Check if the assignee and the category are available in the destination project
     *
     * @access public
     * @param  array      $values
     * @return array
     */
    public function checkDestinationProjectValues(array &$values)
    {
        // Check if the assigned user is allowed for the destination project
        if ($values['owner_id'] > 0 && ! $this->projectPermissionModel->isUserAllowed($values['project_id'], $values['owner_id'])) {
            $values['owner_id'] = 0;
        }

        // Check if the category exists for the destination project
        if ($values['category_id'] > 0) {
            $values['category_id'] = $this->categoryModel->getIdByName(
                $values['project_id'],
                $this->categoryModel->getNameById($values['category_id'])
            );
        }

        // Check if the swimlane exists for the destination project
        $values['swimlane_id'] = $this->swimlaneModel->getIdByName(
            $values['project_id'],
            $this->swimlaneModel->getNameById($values['swimlane_id'])
        );

        if ($values['swimlane_id'] == 0) {
            $values['swimlane_id'] = $this->swimlaneModel->getFirstActiveSwimlaneId($values['project_id']);
        }

        // Check if the column exists for the destination project
        if ($values['column_id'] > 0) {
            $values['column_id'] = $this->columnModel->getColumnIdByTitle(
                $values['project_id'],
                $this->columnModel->getColumnTitleById($values['column_id'])
            );

            $values['column_id'] = $values['column_id'] ?: $this->columnModel->getFirstColumnId($values['project_id']);
        }

        // Check if priority exists for destination project
        $values['priority'] = $this->projectTaskPriorityModel->getPriorityForProject(
            $values['project_id'],
            empty($values['priority']) ? 0 : $values['priority']
        );

        return $values;
    }

    /**
     * Duplicate fields for the new task
     *
     * @access protected
     * @param  integer       $task_id      Task id
     * @return array
     */
    protected function copyFields($task_id)
    {
        $task = $this->taskFinderModel->getById($task_id);
        $values = array();

        foreach ($this->fieldsToDuplicate as $field) {
            $values[$field] = $task[$field];
        }

        return $values;
    }

    /**
     * Create the new task and duplicate subtasks
     *
     * @access protected
     * @param  integer            $task_id      Task id
     * @param  array              $values       Form values
     * @return boolean|integer
     */
    protected function save($task_id, array $values)
    {
        $new_task_id = $this->taskCreationModel->create($values);

        if ($new_task_id !== false) {
            $this->subtaskModel->duplicate($task_id, $new_task_id);
        }

        return $new_task_id;
    }
}
