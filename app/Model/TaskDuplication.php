<?php

namespace Model;

use DateTime;
use DateInterval;
use Event\TaskEvent;

/**
 * Task Duplication
 *
 * @package  model
 * @author   Frederic Guillot
 */
class TaskDuplication extends Base
{
    /**
     * Fields to copy when duplicating a task
     *
     * @access private
     * @var array
     */
    private $fields_to_duplicate = array(
        'title',
        'description',
        'date_due',
        'color_id',
        'project_id',
        'column_id',
        'owner_id',
        'score',
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
        return $this->save($task_id, $this->copyFields($task_id));
    }

    /**
     * Duplicate recurring task
     *
     * @access public
     * @param  integer             $task_id      Task id
     * @return boolean|integer                   Recurrence task id
     */
    public function duplicateRecurringTask($task_id)
    {
        $values = $this->copyFields($task_id);

        if ($values['recurrence_status'] == Task::RECURRING_STATUS_PENDING) {

            $values['recurrence_parent'] = $task_id;
            $values['column_id'] = $this->board->getFirstColumn($values['project_id']);
            $this->calculateRecurringTaskDueDate($values);

            $recurring_task_id = $this->save($task_id, $values);

            if ($recurring_task_id > 0) {

                $parent_update = $this->db
                    ->table(Task::TABLE)
                    ->eq('id', $task_id)
                    ->update(array(
                        'recurrence_status' => Task::RECURRING_STATUS_PROCESSED,
                        'recurrence_child' => $recurring_task_id,
                    ));

                if ($parent_update) {
                    return $recurring_task_id;
                }
            }
        }

        return false;
    }

    /**
     * Duplicate a task to another project
     *
     * @access public
     * @param  integer             $task_id         Task id
     * @param  integer             $project_id      Project id
     * @return boolean|integer                      Duplicated task id
     */
    public function duplicateToProject($task_id, $project_id)
    {
        $values = $this->copyFields($task_id);
        $values['project_id'] = $project_id;
        $values['column_id'] = $this->board->getFirstColumn($project_id);

        $this->checkDestinationProjectValues($values);

        return $this->save($task_id, $values);
    }

    /**
     * Move a task to another project
     *
     * @access public
     * @param  integer    $task_id              Task id
     * @param  integer    $project_id           Project id
     * @return boolean
     */
    public function moveToProject($task_id, $project_id)
    {
        $task = $this->taskFinder->getById($task_id);

        $values = array();
        $values['is_active'] = 1;
        $values['project_id'] = $project_id;
        $values['column_id'] = $this->board->getFirstColumn($project_id);
        $values['position'] = $this->taskFinder->countByColumnId($project_id, $values['column_id']) + 1;
        $values['owner_id'] = $task['owner_id'];
        $values['category_id'] = $task['category_id'];
        $values['swimlane_id'] = $task['swimlane_id'];

        $this->checkDestinationProjectValues($values);

        if ($this->db->table(Task::TABLE)->eq('id', $task['id'])->update($values)) {
            $this->container['dispatcher']->dispatch(
                Task::EVENT_MOVE_PROJECT,
                new TaskEvent(array_merge($task, $values, array('task_id' => $task['id'])))
            );
        }

        return true;
    }

    /**
     * Check if the assignee and the category are available in the destination project
     *
     * @access private
     * @param  array      $values
     */
    private function checkDestinationProjectValues(&$values)
    {
        // Check if the assigned user is allowed for the destination project
        if ($values['owner_id'] > 0 && ! $this->projectPermission->isUserAllowed($values['project_id'], $values['owner_id'])) {
            $values['owner_id'] = 0;
        }

        // Check if the category exists for the destination project
        if ($values['category_id'] > 0) {
            $values['category_id'] = $this->category->getIdByName(
                $values['project_id'],
                $this->category->getNameById($values['category_id'])
            );
        }

        // Check if the swimlane exists for the destination project
        if ($values['swimlane_id'] > 0) {
            $values['swimlane_id'] = $this->swimlane->getIdByName(
                $values['project_id'],
                $this->swimlane->getNameById($values['swimlane_id'])
            );
        }
    }

    /**
     * Calculate new due date for new recurrence task
     *
     * @access public
     * @param  array   $values   Task fields
     */
    public function calculateRecurringTaskDueDate(array &$values)
    {
        if (! empty($values['date_due']) && $values['recurrence_factor'] != 0) {

            if ($values['recurrence_basedate'] == Task::RECURRING_BASEDATE_TRIGGERDATE) {
                $values['date_due'] = time();
            }

            $factor = abs($values['recurrence_factor']);
            $subtract = $values['recurrence_factor'] < 0;

            switch ($values['recurrence_timeframe']) {
                case Task::RECURRING_TIMEFRAME_MONTHS:
                    $interval = 'P' . $factor . 'M';
                    break;
                case Task::RECURRING_TIMEFRAME_YEARS:
                    $interval = 'P' . $factor . 'Y';
                    break;
                default:
                    $interval = 'P' . $factor . 'D';
            }

            $date_due = new DateTime();
            $date_due->setTimestamp($values['date_due']);

            $subtract ? $date_due->sub(new DateInterval($interval)) : $date_due->add(new DateInterval($interval));

            $values['date_due'] = $date_due->getTimestamp();
        }
    }

    /**
     * Duplicate fields for the new task
     *
     * @access private
     * @param  integer       $task_id      Task id
     * @return array
     */
    private function copyFields($task_id)
    {
        $task = $this->taskFinder->getById($task_id);
        $values = array();

        foreach ($this->fields_to_duplicate as $field) {
            $values[$field] = $task[$field];
        }

        return $values;
    }

    /**
     * Create the new task and duplicate subtasks
     *
     * @access private
     * @param  integer            $task_id      Task id
     * @param  array              $values       Form values
     * @return boolean|integer
     */
    private function save($task_id, array $values)
    {
        $new_task_id = $this->taskCreation->create($values);

        if ($new_task_id) {
            $this->subtask->duplicate($task_id, $new_task_id);
        }

        return $new_task_id;
    }
}
