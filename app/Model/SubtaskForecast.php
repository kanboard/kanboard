<?php

namespace Model;

use DateTime;
use DateInterval;

/**
 * Subtask Forecast
 *
 * @package  model
 * @author   Frederic Guillot
 */
class SubtaskForecast extends Base
{
    /**
     * Get not completed subtasks with an estimate sorted by postition
     *
     * @access public
     * @param  integer   $user_id
     * @return array
     */
    public function getSubtasks($user_id)
    {
        return $this->db
                    ->table(Subtask::TABLE)
                    ->columns(Subtask::TABLE.'.id', Task::TABLE.'.project_id', Subtask::TABLE.'.task_id', Subtask::TABLE.'.title', Subtask::TABLE.'.time_estimated')
                    ->join(Task::TABLE, 'id', 'task_id')
                    ->asc(Task::TABLE.'.position')
                    ->asc(Subtask::TABLE.'.position')
                    ->gt(Subtask::TABLE.'.time_estimated', 0)
                    ->eq(Subtask::TABLE.'.status', Subtask::STATUS_TODO)
                    ->eq(Subtask::TABLE.'.user_id', $user_id)
                    ->findAll();
    }

    /**
     * Get the start date for the forecast
     *
     * @access public
     * @param  integer   $user_id
     * @return array
     */
    public function getStartDate($user_id)
    {
        $subtask = $this->db->table(Subtask::TABLE)
                            ->columns(Subtask::TABLE.'.time_estimated', SubtaskTimeTracking::TABLE.'.start')
                            ->eq(SubtaskTimeTracking::TABLE.'.user_id', $user_id)
                            ->eq(SubtaskTimeTracking::TABLE.'.end', 0)
                            ->status('status', Subtask::STATUS_INPROGRESS)
                            ->join(SubtaskTimeTracking::TABLE, 'subtask_id', 'id')
                            ->findOne();

        if ($subtask && $subtask['time_estimated'] && $subtask['start']) {
            return date('Y-m-d H:i', $subtask['start'] + $subtask['time_estimated'] * 3600);
        }

        return date('Y-m-d H:i');
    }

    /**
     * Get all calendar events according to the user timetable and the subtasks estimates
     *
     * @access public
     * @param  integer   $user_id
     * @param  string    $end         End date of the calendar
     * @return array
     */
    public function getCalendarEvents($user_id, $end)
    {
        $events = array();
        $start_date = new DateTime($this->getStartDate($user_id));
        $timetable = $this->timetable->calculate($user_id, $start_date, new DateTime($end));
        $subtasks = $this->getSubtasks($user_id);
        $total = count($subtasks);
        $offset = 0;

        foreach ($timetable as $slot) {

            $interval = $this->dateParser->getHours($slot[0], $slot[1]);
            $start = $slot[0]->getTimestamp();

            if ($slot[0] < $start_date) {

                if (! $this->dateParser->withinDateRange($start_date, $slot[0], $slot[1])) {
                    continue;
                }

                $interval = $this->dateParser->getHours(new DateTime, $slot[1]);
                $start = time();
            }

            while ($offset < $total) {

                $event = array(
                    'id' => $subtasks[$offset]['id'].'-'.$subtasks[$offset]['task_id'].'-'.$offset,
                    'subtask_id' => $subtasks[$offset]['id'],
                    'title' => t('#%d', $subtasks[$offset]['task_id']).' '.$subtasks[$offset]['title'],
                    'url' => $this->helper->url('task', 'show', array('task_id' => $subtasks[$offset]['task_id'], 'project_id' => $subtasks[$offset]['project_id'])),
                    'editable' => false,
                    'start' => date('Y-m-d\TH:i:s', $start),
                );

                if ($subtasks[$offset]['time_estimated'] <= $interval) {

                    $start += $subtasks[$offset]['time_estimated'] * 3600;
                    $interval -= $subtasks[$offset]['time_estimated'];
                    $offset++;

                    $event['end'] = date('Y-m-d\TH:i:s', $start);
                    $events[] = $event;
                }
                else {
                    $subtasks[$offset]['time_estimated'] -= $interval;
                    $event['end'] = $slot[1]->format('Y-m-d\TH:i:s');
                    $events[] = $event;
                    break;
                }
            }
        }

        return $events;
    }
}
