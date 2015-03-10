<?php

namespace Model;

use DateTime;
use DateInterval;

/**
 * Timetable
 *
 * @package  model
 * @author   Frederic Guillot
 */
class Timetable extends Base
{
    /**
     * User time slots
     *
     * @access private
     * @var array
     */
    private $day;
    private $week;
    private $overtime;
    private $timeoff;

    /**
     * Get the timetable for a user for a given date range
     *
     * @access public
     * @param  integer     $user_id
     * @param  \DateTime   $start
     * @param  \DateTime   $end
     * @return array
     */
    public function calculate($user_id, DateTime $start, DateTime $end)
    {
        $timetable = array();

        $this->day = $this->timetableDay->getByUser($user_id);
        $this->week = $this->timetableWeek->getByUser($user_id);
        $this->overtime = $this->timetableExtra->getByUserAndDate($user_id, $start->format('Y-m-d'), $end->format('Y-m-d'));
        $this->timeoff = $this->timetableOff->getByUserAndDate($user_id, $start->format('Y-m-d'), $end->format('Y-m-d'));

        for ($today = clone($start); $today <= $end; $today->add(new DateInterval('P1D'))) {
            $week_day = $today->format('N');
            $timetable = array_merge($timetable, $this->getWeekSlots($today, $week_day));
            $timetable = array_merge($timetable, $this->getOvertimeSlots($today, $week_day));
        }

        return $timetable;
    }

    /**
     * Return worked time slots for the given day
     *
     * @access public
     * @param  \DateTime   $today
     * @param  string      $week_day
     * @return array
     */
    public function getWeekSlots(DateTime $today, $week_day)
    {
        $slots = array();
        $dayoff = $this->getDayOff($today);

        if (! empty($dayoff) && $dayoff['all_day'] == 1) {
            return array();
        }

        foreach ($this->week as $slot) {
            if ($week_day == $slot['day']) {
                $slots = array_merge($slots, $this->getDayWorkSlots($slot, $dayoff, $today));
            }
        }

        return $slots;
    }

    /**
     * Get the overtime time slots for the given day
     *
     * @access public
     * @param  \DateTime   $today
     * @param  string      $week_day
     * @return array
     */
    public function getOvertimeSlots(DateTime $today, $week_day)
    {
        $slots = array();

        foreach ($this->overtime as $slot) {

            $day = new DateTime($slot['date']);

            if ($week_day == $day->format('N')) {

                if ($slot['all_day'] == 1) {
                    $slots = array_merge($slots, $this->getDaySlots($today));
                }
                else {
                    $slots[] = $this->getTimeSlot($slot, $day);
                }
            }
        }

        return $slots;
    }

    /**
     * Get worked time slots and remove time off
     *
     * @access public
     * @param  array       $slot
     * @param  array       $dayoff
     * @param  \DateTime   $today
     * @return array
     */
    public function getDayWorkSlots(array $slot, array $dayoff, DateTime $today)
    {
        $slots = array();

        if (! empty($dayoff) && $dayoff['start'] < $slot['end']) {

            if ($dayoff['start'] > $slot['start']) {
                $slots[] = $this->getTimeSlot(array('end' => $dayoff['start']) + $slot, $today);
            }

            if ($dayoff['end'] < $slot['end']) {
                $slots[] = $this->getTimeSlot(array('start' => $dayoff['end']) + $slot, $today);
            }
        }
        else {
            $slots[] = $this->getTimeSlot($slot, $today);
        }

        return $slots;
    }

    /**
     * Get regular day work time slots
     *
     * @access public
     * @param  \DateTime   $today
     * @return array
     */
    public function getDaySlots(DateTime $today)
    {
        $slots = array();

        foreach ($this->day as $day) {
            $slots[] = $this->getTimeSlot($day, $today);
        }

        return $slots;
    }

    /**
     * Get the start and end time slot for a given day
     *
     * @access public
     * @param  array       $slot
     * @param  \DateTime   $today
     * @return array
     */
    public function getTimeSlot(array $slot, DateTime $today)
    {
        $date = $today->format('Y-m-d');

        return array(
            new DateTime($date.' '.$slot['start']),
            new DateTime($date.' '.$slot['end']),
        );
    }

    /**
     * Return day off time slot
     *
     * @access public
     * @param  \DateTime   $today
     * @return array
     */
    public function getDayOff(DateTime $today)
    {
        foreach ($this->timeoff as $day) {

            if ($day['date'] === $today->format('Y-m-d')) {
                return $day;
            }
        }

        return array();
    }
}
