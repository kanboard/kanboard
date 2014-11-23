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
