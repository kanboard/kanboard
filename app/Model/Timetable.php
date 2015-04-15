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
     * Get a set of events by using the intersection between the timetable and the time tracking data
     *
     * @access public
     * @param  integer  $user_id
     * @param  array    $events     Time tracking data
     * @param  string   $start      ISO8601 date
     * @param  string   $end        ISO8601 date
     * @return array
     */
    public function calculateEventsIntersect($user_id, array $events, $start, $end)
    {
        $start_dt = new DateTime($start);
        $start_dt->setTime(0, 0);

        $end_dt = new DateTime($end);
        $end_dt->setTime(23, 59);

        $timetable = $this->calculate($user_id, $start_dt, $end_dt);

        // The user has no timetable
        if (empty($this->week)) {
            return $events;
        }

        $results = array();

        foreach ($events as $event) {
            $results = array_merge($results, $this->calculateEventIntersect($event, $timetable));
        }

        return $results;
    }

    /**
     * Get a serie of events based on the timetable and the provided event
     *
     * @access public
     * @param  array    $event
     * @param  array    $timetable
     * @return array
     */
    public function calculateEventIntersect(array $event, array $timetable)
    {
        $events = array();

        foreach ($timetable as $slot) {

            $start_ts = $slot[0]->getTimestamp();
            $end_ts = $slot[1]->getTimestamp();

            if ($start_ts > $event['end']) {
                break;
            }

            if ($event['start'] <= $start_ts) {
                $event['start'] = $start_ts;
            }

            if ($event['start'] >= $start_ts && $event['start'] <= $end_ts) {

                if ($event['end'] >= $end_ts) {
                    $events[] = array_merge($event, array('end' => $end_ts));
                }
                else {
                    $events[] = $event;
                    break;
                }
            }
        }

        return $events;
    }

    /**
     * Calculate effective worked hours by taking into consideration the timetable
     *
     * @access public
     * @param  integer     $user_id
     * @param  \DateTime   $start
     * @param  \DateTime   $end
     * @return float
     */
    public function calculateEffectiveDuration($user_id, DateTime $start, DateTime $end)
    {
        $end_timetable = clone($end);
        $end_timetable->setTime(23, 59);

        $timetable = $this->calculate($user_id, $start, $end_timetable);
        $found_start = false;
        $hours = 0;

        // The user has no timetable
        if (empty($this->week)) {
            return $this->dateParser->getHours($start, $end);
        }

        foreach ($timetable as $slot) {

            $isStartSlot = $this->dateParser->withinDateRange($start, $slot[0], $slot[1]);
            $isEndSlot = $this->dateParser->withinDateRange($end, $slot[0], $slot[1]);

            // Start and end are within the same time slot
            if ($isStartSlot && $isEndSlot) {
                return $this->dateParser->getHours($start, $end);
            }

            // We found the start slot
            if (! $found_start && $isStartSlot) {
                $found_start = true;
                $hours = $this->dateParser->getHours($start, $slot[1]);
            }
            else if ($found_start) {

                // We found the end slot
                if ($isEndSlot) {
                    $hours += $this->dateParser->getHours($slot[0], $end);
                    break;
                }
                else {

                    // Sum hours of the intermediate time slots
                    $hours += $this->dateParser->getHours($slot[0], $slot[1]);
                }
            }
        }

        // The start date was not found in regular hours so we get the nearest time slot
        if (! empty($timetable) && ! $found_start) {
            $slot = $this->findClosestTimeSlot($start, $timetable);

            if ($start < $slot[0]) {
                return $this->calculateEffectiveDuration($user_id, $slot[0], $end);
            }
        }

        return $hours;
    }

    /**
     * Find the nearest time slot
     *
     * @access public
     * @param  DateTime  $date
     * @param  array     $timetable
     * @return array
     */
    public function findClosestTimeSlot(DateTime $date, array $timetable)
    {
        $values = array();

        foreach ($timetable as $slot) {
            $t1 = abs($slot[0]->getTimestamp() - $date->getTimestamp());
            $t2 = abs($slot[1]->getTimestamp() - $date->getTimestamp());

            $values[] = min($t1, $t2);
        }

        asort($values);
        return $timetable[key($values)];
    }

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
