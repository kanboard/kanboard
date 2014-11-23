<?php

namespace Model;

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

        $this->checkDestinationProjectValues($values);

        return $this->db->table(Task::TABLE)->eq('id', $task['id'])->update($values);
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
            $category_name = $this->category->getNameById($values['category_id']);
            $values['category_id'] = $this->category->getIdByName($values['project_id'], $category_name);
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
            $this->subTask->duplicate($task_id, $new_task_id);
        }

        return $new_task_id;
    }
}
