<?php

namespace Helper;

/**
 * DateTime helpers
 *
 * @package helper
 * @author  Frederic Guillot
 */
class Datetime extends \Core\Base
{
    /**
     * Get all hours for day
     *
     * @access public
     * @return array
     */
    public function getDayHours()
    {
        $values = array();

        foreach (range(0, 23) as $hour) {
            foreach (array(0, 30) as $minute) {
                $time = sprintf('%02d:%02d', $hour, $minute);
                $values[$time] = $time;
            }
        }

        return $values;
    }

    /**
     * Get all days of a week
     *
     * @access public
     * @return array
     */
    public function getWeekDays()
    {
        $values = array();

        foreach (range(1, 7) as $day) {
            $values[$day] = $this->getWeekDay($day);
        }

        return $values;
    }

    /**
     * Get the localized day name from the day number
     *
     * @access public
     * @param  integer   $day  Day number
     * @return string
     */
    public function getWeekDay($day)
    {
        return dt('%A', strtotime('next Monday +'.($day - 1).' days'));
    }
}
