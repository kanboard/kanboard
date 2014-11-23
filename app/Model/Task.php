<?php

namespace Model;

/**
 * Task model
 *
 * @package  model
 * @author   Frederic Guillot
 */
class Task extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE               = 'tasks';

    /**
     * Task status
     *
     * @var integer
     */
    const STATUS_OPEN         = 1;
    const STATUS_CLOSED       = 0;

    /**
     * Events
     *
     * @var string
     */
    const EVENT_MOVE_COLUMN     = 'task.move.column';
    const EVENT_MOVE_POSITION   = 'task.move.position';
    const EVENT_UPDATE          = 'task.update';
    const EVENT_CREATE          = 'task.create';
    const EVENT_CLOSE           = 'task.close';
    const EVENT_OPEN            = 'task.open';
    const EVENT_CREATE_UPDATE   = 'task.create_update';
    const EVENT_ASSIGNEE_CHANGE = 'task.assignee_change';

    /**
     * Remove a task
     *
     * @access public
     * @param  integer   $task_id   Task id
     * @return boolean
     */
    public function remove($task_id)
    {
        if (! $this->taskFinder->exists($task_id)) {
            return false;
        }

        $this->file->removeAll($task_id);

        return $this->db->table(self::TABLE)->eq('id', $task_id)->remove();
    }

    /**
     * Move a task to another project
     *
     * @access public
     * @param  integer    $project_id           Project id
     * @param  array      $task                 Task data
     * @return boolean
     */
    public function moveToAnotherProject($project_id, array $task)
    {
        $values = array();

        // Clear values (categories are different for each project)
        $values['category_id'] = 0;
        $values['owner_id'] = 0;

        // Check if the assigned user is allowed for the new project
        if ($task['owner_id'] && $this->projectPermission->isUserAllowed($project_id, $task['owner_id'])) {
            $values['owner_id'] = $task['owner_id'];
        }

        // We use the first column of the new project
        $values['column_id'] = $this->board->getFirstColumn($project_id);
        $values['position'] = $this->taskFinder->countByColumnId($project_id, $values['column_id']) + 1;
        $values['project_id'] = $project_id;

        // The task will be open (close event binding)
        $values['is_active'] = 1;

        if ($this->db->table(self::TABLE)->eq('id', $task['id'])->update($values)) {
            return $task['id'];
        }

        return false;
    }

    /**
     * Generic method to duplicate a task
     *
     * @access public
     * @param  array      $task         Task data
     * @param  array      $override     Task properties to override
     * @return integer|boolean
     */
    public function copy(array $task, array $override = array())
    {
        // Values to override
        if (! empty($override)) {
            $task = $override + $task;
        }

        $this->db->startTransaction();

        // Assign new values
        $values = array();
        $values['title'] = $task['title'];
        $values['description'] = $task['description'];
        $values['date_creation'] = time();
        $values['date_modification'] = $values['date_creation'];
        $values['date_due'] = $task['date_due'];
        $values['color_id'] = $task['color_id'];
        $values['project_id'] = $task['project_id'];
        $values['column_id'] = $task['column_id'];
        $values['owner_id'] = 0;
        $values['creator_id'] = $task['creator_id'];
        $values['position'] = $this->taskFinder->countByColumnId($values['project_id'], $values['column_id']) + 1;
        $values['score'] = $task['score'];
        $values['category_id'] = 0;

        // Check if the assigned user is allowed for the new project
        if ($task['owner_id'] && $this->projectPermission->isUserAllowed($values['project_id'], $task['owner_id'])) {
            $values['owner_id'] = $task['owner_id'];
        }

        // Check if the category exists
        if ($task['category_id'] && $this->category->exists($task['category_id'], $task['project_id'])) {
            $values['category_id'] = $task['category_id'];
        }

        // Save task
        if (! $this->db->table(Task::TABLE)->save($values)) {
            $this->db->cancelTransaction();
            return false;
        }

        $task_id = $this->db->getConnection()->getLastId();

        // Duplicate subtasks
        if (! $this->subTask->duplicate($task['id'], $task_id)) {
            $this->db->cancelTransaction();
            return false;
        }

        $this->db->closeTransaction();

        // Trigger events
        $this->event->trigger(Task::EVENT_CREATE_UPDATE, array('task_id' => $task_id) + $values);
        $this->event->trigger(Task::EVENT_CREATE, array('task_id' => $task_id) + $values);

        return $task_id;
    }

    /**
     * Duplicate a task to the same project
     *
     * @access public
     * @param  array      $task         Task data
     * @return integer|boolean
     */
    public function duplicateToSameProject($task)
    {
        return $this->copy($task);
    }

    /**
     * Duplicate a task to another project (always copy to the first column)
     *
     * @access public
     * @param  integer    $project_id   Destination project id
     * @param  array      $task         Task data
     * @return integer|boolean
     */
    public function duplicateToAnotherProject($project_id, array $task)
    {
        return $this->copy($task, array(
            'project_id' => $project_id,
            'column_id' => $this->board->getFirstColumn($project_id),
        ));
    }

    /**
     * Get a the task id from a text
     *
     * Example: "Fix bug #1234" will return 1234
     *
     * @access public
     * @param  string   $message   Text
     * @return integer
     */
    public function getTaskIdFromText($message)
    {
        if (preg_match('!#(\d+)!i', $message, $matches) && isset($matches[1])) {
            return $matches[1];
        }

        return 0;
    }
}
