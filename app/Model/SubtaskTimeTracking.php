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
                        Subtask::TABLE.'.task_id',
                        Subtask::TABLE.'.title AS subtask_title',
                        Task::TABLE.'.title AS task_title',
                        Task::TABLE.'.project_id'
                    )
                    ->join(Subtask::TABLE, 'id', 'subtask_id')
                    ->join(Task::TABLE, 'id', 'task_id', Subtask::TABLE)
                    ->eq(self::TABLE.'.user_id', $user_id);
    }

    /**
     * Get query for task (pagination)
     *
     * @access public
     * @param  integer    $task_id       Task id
     * @return \PicoDb\Table
     */
    public function getTaskQuery($task_id)
    {
        return $this->db
                    ->table(self::TABLE)
                    ->columns(
                        self::TABLE.'.id',
                        self::TABLE.'.subtask_id',
                        self::TABLE.'.end',
                        self::TABLE.'.start',
                        self::TABLE.'.user_id',
                        Subtask::TABLE.'.task_id',
                        Subtask::TABLE.'.title AS subtask_title',
                        Task::TABLE.'.project_id',
                        User::TABLE.'.username',
                        User::TABLE.'.name AS user_fullname'
                    )
                    ->join(Subtask::TABLE, 'id', 'subtask_id')
                    ->join(Task::TABLE, 'id', 'task_id', Subtask::TABLE)
                    ->join(User::TABLE, 'id', 'user_id', self::TABLE)
                    ->eq(Task::TABLE.'.id', $task_id);
    }

    /**
     * Get all recorded time slots for a given user
     *
     * @access public
     * @param  integer    $user_id       User id
     * @return array
     */
    public function getUserTimesheet($user_id)
    {
        return $this->db
                    ->table(self::TABLE)
                    ->eq('user_id', $user_id)
                    ->findAll();
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
        $this->updateSubtaskTimeSpent($subtask_id, $user_id);

        return $this->db
                    ->table(self::TABLE)
                    ->eq('subtask_id', $subtask_id)
                    ->eq('user_id', $user_id)
                    ->eq('end', 0)
                    ->update(array(
                        'end' => time()
                    ));
    }

    /**
     * Update task time tracking based on subtasks time tracking
     *
     * @access public
     * @param  integer   $task_id    Task id
     * @return bool
     */
    public function updateTaskTimeTracking($task_id)
    {
        $result = $this->calculateSubtaskTime($task_id); 

        if (empty($result['total_spent']) && empty($result['total_estimated'])) {
            return true;
        }

        return $this->db
                    ->table(Task::TABLE)
                    ->eq('id', $task_id)
                    ->update(array(
                        'time_spent' => $result['total_spent'],
                        'time_estimated' => $result['total_estimated'],
                    ));
    }

    /**
     * Sum time spent and time estimated for all subtasks
     *
     * @access public
     * @param  integer   $task_id    Task id
     * @return array
     */
    public function calculateSubtaskTime($task_id)
    {
        return $this->db
                    ->table(Subtask::TABLE)
                    ->eq('task_id', $task_id)
                    ->columns(
                        'SUM(time_spent) AS total_spent',
                        'SUM(time_estimated) AS total_estimated'
                    )
                    ->findOne();
    }

    /**
     * Update subtask time spent based on the punch clock table
     *
     * @access public
     * @param  integer   $subtask_id
     * @param  integer   $user_id
     * @return bool
     */
    public function updateSubtaskTimeSpent($subtask_id, $user_id)
    {
        $start_time = $this->db
                           ->table(self::TABLE)
                           ->eq('subtask_id', $subtask_id)
                           ->eq('user_id', $user_id)
                           ->eq('end', 0)
                           ->findOneColumn('start');

        $time_spent = $this->db
                           ->table(Subtask::TABLE)
                           ->eq('id', $subtask_id)
                           ->findOneColumn('time_spent');

        return $start_time &&
               $this->db
                    ->table(Subtask::TABLE)
                    ->eq('id', $subtask_id)
                    ->update(array(
                        'time_spent' => $time_spent + round((time() - $start_time) / 3600, 1)
                    ));
    }
}
