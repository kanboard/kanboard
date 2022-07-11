<?php

namespace Kanboard\Model;

use DateTime;
use Kanboard\Core\Base;

/**
 * Subtask time tracking
 *
 * @package  Kanboard\Model
 * @author   Frederic Guillot
 */
class SubtaskTimeTrackingModel extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'subtask_time_tracking';

    /**
     * Get query to check if a timer is started for the given user and subtask
     *
     * @access public
     * @param  integer    $user_id   User id
     * @return string
     */
    public function getTimerQuery($user_id)
    {
        $sql = $this->db
                    ->table(self::TABLE)
                    ->columns('start')
                    ->eq($this->db->escapeIdentifier('user_id',self::TABLE), $user_id)
                    ->eq($this->db->escapeIdentifier('end',self::TABLE), 0)
                    ->eq($this->db->escapeIdentifier('subtask_id',self::TABLE), SubtaskModel::TABLE.'.id')
                    ->limit(1)
                    ->buildSelectQuery();
        // need to interpolate values into the SQL text for use as a subquery
        // in SubtaskModel::getQuery()
        $sql = substr_replace($sql, $user_id, strpos($sql, '?'), 1);
        $sql = substr_replace($sql, 0, strpos($sql, '?'), 1);
        $sql = substr_replace($sql, SubtaskModel::TABLE.'.id', strpos($sql, '?'), 1);
        return $sql;
    }

    /**
     * Get query for user timesheet (pagination)
     *
     * @access public
     * @param  integer    $user_id   User id
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
                        self::TABLE.'.time_spent',
                        SubtaskModel::TABLE.'.task_id',
                        SubtaskModel::TABLE.'.title AS subtask_title',
                        TaskModel::TABLE.'.title AS task_title',
                        TaskModel::TABLE.'.project_id',
                        TaskModel::TABLE.'.color_id'
                    )
                    ->join(SubtaskModel::TABLE, 'id', 'subtask_id')
                    ->join(TaskModel::TABLE, 'id', 'task_id', SubtaskModel::TABLE)
                    ->eq(self::TABLE.'.user_id', $user_id);
    }

    /**
     * Get query for task timesheet (pagination)
     *
     * @access public
     * @param  integer    $task_id    Task id
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
                        self::TABLE.'.time_spent',
                        self::TABLE.'.user_id',
                        SubtaskModel::TABLE.'.task_id',
                        SubtaskModel::TABLE.'.title AS subtask_title',
                        TaskModel::TABLE.'.project_id',
                        UserModel::TABLE.'.username',
                        UserModel::TABLE.'.name AS user_fullname'
                    )
                    ->join(SubtaskModel::TABLE, 'id', 'subtask_id')
                    ->join(TaskModel::TABLE, 'id', 'task_id', SubtaskModel::TABLE)
                    ->join(UserModel::TABLE, 'id', 'user_id', self::TABLE)
                    ->eq(TaskModel::TABLE.'.id', $task_id);
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
     * Return true if a timer is started for this use and subtask
     *
     * @access public
     * @param  integer  $subtask_id
     * @param  integer  $user_id
     * @return boolean
     */
    public function hasTimer($subtask_id, $user_id)
    {
        return $this->db->table(self::TABLE)->eq('subtask_id', $subtask_id)->eq('user_id', $user_id)->eq('end', 0)->exists();
    }

    /**
     * Start or stop timer according to subtask status
     *
     * @access public
     * @param  integer $subtask_id
     * @param  integer $user_id
     * @param  integer $status
     * @return boolean
     */
    public function toggleTimer($subtask_id, $user_id, $status)
    {
        if ($this->configModel->get('subtask_time_tracking') == 1) {
            if ($status == SubtaskModel::STATUS_INPROGRESS) {
                return $this->subtaskTimeTrackingModel->logStartTime($subtask_id, $user_id);
            } elseif ($status == SubtaskModel::STATUS_DONE) {
                return $this->subtaskTimeTrackingModel->logEndTime($subtask_id, $user_id);
            }
        }

        return false;
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
        return
            ! $this->hasTimer($subtask_id, $user_id) &&
            $this->db
                ->table(self::TABLE)
                ->insert(array('subtask_id' => $subtask_id, 'user_id' => $user_id, 'start' => time(), 'end' => 0));
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
        $time_spent = $this->getTimeSpent($subtask_id, $user_id);

        if ($time_spent > 0) {
            $this->updateSubtaskTimeSpent($subtask_id, $time_spent);
        }

        return $this->db
                    ->table(self::TABLE)
                    ->eq('subtask_id', $subtask_id)
                    ->eq('user_id', $user_id)
                    ->eq('end', 0)
                    ->update(array(
                        'end' => time(),
                        'time_spent' => $time_spent,
                    ));
    }

    /**
     * Calculate the time spent when the clock is stopped
     *
     * @access public
     * @param  integer   $subtask_id
     * @param  integer   $user_id
     * @return float
     */
    public function getTimeSpent($subtask_id, $user_id)
    {
        $hook = 'model:subtask-time-tracking:calculate:time-spent';
        $start_time = $this->db
            ->table(self::TABLE)
            ->eq('subtask_id', $subtask_id)
            ->eq('user_id', $user_id)
            ->eq('end', 0)
            ->findOneColumn('start');

        if (empty($start_time)) {
            return 0;
        }

        $end = new DateTime;
        $start = new DateTime;
        $start->setTimestamp($start_time);

        if ($this->hook->exists($hook)) {
            return $this->hook->first($hook, array(
                'user_id' => $user_id,
                'start' => $start,
                'end' => $end,
            ));
        }

        return $this->dateParser->getHours($start, $end);
    }

    /**
     * Update subtask time spent
     *
     * @access public
     * @param  integer   $subtask_id
     * @param  float     $time_spent
     * @return bool
     */
    public function updateSubtaskTimeSpent($subtask_id, $time_spent)
    {
        $subtask = $this->subtaskModel->getById($subtask_id);

        return $this->subtaskModel->update(array(
            'id' => $subtask['id'],
            'time_spent' => $subtask['time_spent'] + $time_spent,
            'task_id' => $subtask['task_id'],
        ), false);
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
        $values = $this->calculateSubtaskTime($task_id);

        return $this->db
                    ->table(TaskModel::TABLE)
                    ->eq('id', $task_id)
                    ->update($values);
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
                    ->table(SubtaskModel::TABLE)
                    ->eq('task_id', $task_id)
                    ->columns(
                        'SUM(time_spent) AS time_spent',
                        'SUM(time_estimated) AS time_estimated'
                    )
                    ->findOne();
    }
}
