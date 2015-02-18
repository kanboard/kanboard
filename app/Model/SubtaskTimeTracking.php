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
                    ->eq(Task::TABLE.'.project_id', $project_id);
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
     * @param  integer   $start
     * @param  integer   $end
     * @return array
     */
    public function getUserCalendarEvents($user_id, $start, $end)
    {
        $result = $this->getUserQuery($user_id)
                       ->addCondition($this->getCalendarCondition($start, $end))
                       ->findAll();

        return $this->toCalendarEvents($result);
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
        $result = $this->getProjectQuery($project_id)
                       ->addCondition($this->getCalendarCondition($start, $end))
                       ->findAll();

        return $this->toCalendarEvents($result);
    }

    /**
     * Get time slots that should be displayed in the calendar time range
     *
     * @access private
     * @param  string   $start   ISO8601 start date
     * @param  string   $end     ISO8601 end date
     * @return string
     */
    private function getCalendarCondition($start, $end)
    {
        $start_time = $this->dateParser->getTimestampFromIsoFormat($start);
        $end_time = $this->dateParser->getTimestampFromIsoFormat($end);
        $start_column = $this->db->escapeIdentifier('start');
        $end_column = $this->db->escapeIdentifier('end');

        $conditions = array(
            "($start_column >= '$start_time' AND $start_column <= '$end_time')",
            "($start_column <= '$start_time' AND $end_column >= '$start_time')",
            "($start_column <= '$start_time' AND $end_column = '0')",
        );

        return '('.implode(' OR ', $conditions).')';
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
                'url' => $this->helper->url('task', 'show', array('task_id' => $row['task_id'], 'project_id' => $row['project_id'])),
                'editable' => false,
            );
        }

        return $events;
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

        $subtask = $this->subtask->getById($subtask_id);

        return $start_time &&
               $this->subtask->update(array(      // Fire the event subtask.update
                    'id' => $subtask['id'],
                    'time_spent' => $subtask['time_spent'] + round((time() - $start_time) / 3600, 1),
                    'task_id' => $subtask['task_id'],
               ));
    }
}
