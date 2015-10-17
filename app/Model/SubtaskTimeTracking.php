<?php

namespace Kanboard\Model;

use DateTime;

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
     * Get query to check if a timer is started for the given user and subtask
     *
     * @access public
     * @param  integer    $user_id   User id
     * @return string
     */
    public function getTimerQuery($user_id)
    {
        return sprintf(
            "SELECT %s FROM %s WHERE %s='%d' AND %s='0' AND %s=%s LIMIT 1",
            $this->db->escapeIdentifier('start'),
            $this->db->escapeIdentifier(self::TABLE),
            $this->db->escapeIdentifier('user_id'),
            $user_id,
            $this->db->escapeIdentifier('end'),
            $this->db->escapeIdentifier('subtask_id'),
            Subtask::TABLE.'.id'
        );
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
                        Subtask::TABLE.'.task_id',
                        Subtask::TABLE.'.title AS subtask_title',
                        Task::TABLE.'.title AS task_title',
                        Task::TABLE.'.project_id',
                        Task::TABLE.'.color_id'
                    )
                    ->join(Subtask::TABLE, 'id', 'subtask_id')
                    ->join(Task::TABLE, 'id', 'task_id', Subtask::TABLE)
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
     * Get query for project timesheet (pagination)
     *
     * @access public
     * @param  integer    $project_id   Project id
     * @return \PicoDb\Table
     */
    public function getProjectQuery($project_id)
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
                        Subtask::TABLE.'.task_id',
                        Subtask::TABLE.'.title AS subtask_title',
                        Task::TABLE.'.project_id',
                        Task::TABLE.'.color_id',
                        User::TABLE.'.username',
                        User::TABLE.'.name AS user_fullname'
                    )
                    ->join(Subtask::TABLE, 'id', 'subtask_id')
                    ->join(Task::TABLE, 'id', 'task_id', Subtask::TABLE)
                    ->join(User::TABLE, 'id', 'user_id', self::TABLE)
                    ->eq(Task::TABLE.'.project_id', $project_id)
                    ->asc(self::TABLE.'.id');
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
     * Get user calendar events
     *
     * @access public
     * @param  integer   $user_id
     * @param  string    $start      ISO-8601 format
     * @param  string    $end
     * @return array
     */
    public function getUserCalendarEvents($user_id, $start, $end)
    {
        $hook = 'model:subtask-time-tracking:calendar:events';
        $events = $this->getUserQuery($user_id)
            ->addCondition($this->getCalendarCondition(
                $this->dateParser->getTimestampFromIsoFormat($start),
                $this->dateParser->getTimestampFromIsoFormat($end),
                'start',
                'end'
            ))
            ->findAll();

        if ($this->hook->exists($hook)) {
            $events = $this->hook->first($hook, array(
                'user_id' => $user_id,
                'events' => $events,
                'start' => $start,
                'end' => $end,
            ));
        }

        return $this->toCalendarEvents($events);
    }

    /**
     * Get project calendar events
     *
     * @access public
     * @param  integer   $project_id
     * @param  integer   $start
     * @param  integer   $end
     * @return array
     */
    public function getProjectCalendarEvents($project_id, $start, $end)
    {
        $result = $this
            ->getProjectQuery($project_id)
            ->addCondition($this->getCalendarCondition(
                $this->dateParser->getTimestampFromIsoFormat($start),
                $this->dateParser->getTimestampFromIsoFormat($end),
                'start',
                'end'
            ))
            ->findAll();

        return $this->toCalendarEvents($result);
    }

    /**
     * Convert a record set to calendar events
     *
     * @access private
     * @param  array    $rows
     * @return array
     */
    private function toCalendarEvents(array $rows)
    {
        $events = array();

        foreach ($rows as $row) {
            $user = isset($row['username']) ? ' ('.($row['user_fullname'] ?: $row['username']).')' : '';

            $events[] = array(
                'id' => $row['id'],
                'subtask_id' => $row['subtask_id'],
                'title' => t('#%d', $row['task_id']).' '.$row['subtask_title'].$user,
                'start' => date('Y-m-d\TH:i:s', $row['start']),
                'end' => date('Y-m-d\TH:i:s', $row['end'] ?: time()),
                'backgroundColor' => $this->color->getBackgroundColor($row['color_id']),
                'borderColor' => $this->color->getBorderColor($row['color_id']),
                'textColor' => 'black',
                'url' => $this->helper->url->to('task', 'show', array('task_id' => $row['task_id'], 'project_id' => $row['project_id'])),
                'editable' => false,
            );
        }

        return $events;
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
        $subtask = $this->subtask->getById($subtask_id);

        // Fire the event subtask.update
        return $this->subtask->update(array(
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
                    ->table(Task::TABLE)
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
                    ->table(Subtask::TABLE)
                    ->eq('task_id', $task_id)
                    ->columns(
                        'SUM(time_spent) AS time_spent',
                        'SUM(time_estimated) AS time_estimated'
                    )
                    ->findOne();
    }
}
