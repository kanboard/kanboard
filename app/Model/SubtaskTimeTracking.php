<?php

namespace Model;

/**
 * Subtask timesheet
 *
 * @package  model
 * @author   Frederic Guillot
 */
class SubtaskTimeTracking extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'subtask_time_tracking';

    /**
     * Get query for user timesheet (pagination)
     *
     * @access public
     * @param  integer    $user_id       User id
     * @return \PicoDb\Table
     */
    public function getUserQuery($user_id)
    {
        return $this->db
                    ->table(self::TABLE)
                    ->columns(
                        self::TABLE.'.id',
                        self::TABLE.'.subtask_id',
                        self::TABLE.'.end',
                        self::TABLE.'.start',
                        SubTask::TABLE.'.task_id',
                        SubTask::TABLE.'.title AS subtask_title',
                        Task::TABLE.'.title AS task_title',
                        Task::TABLE.'.project_id'
                    )
                    ->join(SubTask::TABLE, 'id', 'subtask_id')
                    ->join(Task::TABLE, 'id', 'task_id', SubTask::TABLE)
                    ->eq(self::TABLE.'.user_id', $user_id);
    }

    /**
     * Log start time
     *
     * @access public
     * @param  integer   $subtask_id
     * @param  integer   $user_id
     * @return boolean
     */
    public function logStartTime($subtask_id, $user_id)
    {
        return $this->db
                    ->table(self::TABLE)
                    ->insert(array('subtask_id' => $subtask_id, 'user_id' => $user_id, 'start' => time()));
    }

    /**
     * Log end time
     *
     * @access public
     * @param  integer   $subtask_id
     * @param  integer   $user_id
     * @return boolean
     */
    public function logEndTime($subtask_id, $user_id)
    {
        return $this->db
                    ->table(self::TABLE)
                    ->eq('subtask_id', $subtask_id)
                    ->eq('user_id', $user_id)
                    ->eq('end', 0)
                    ->update(array(
                        'end' => time()
                    ));
    }
}
