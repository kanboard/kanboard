<?php

namespace Kanboard\Core;

use DateTime;

/**
 * Date Parser
 *
 * @package  core
 * @author   Frederic Guillot
 */
class DateParser extends Base
{
    /**
     * Return true if the date is within the date range
     *
     * @access public
     * @param  DateTime  $date
     * @param  DateTime  $start
     * @param  DateTime  $end
     * @return boolean
     */
    public function withinDateRange(DateTime $date, DateTime $start, DateTime $end)
    {
        return $date >= $start && $date <= $end;
    }

    /**
     * Get the total number of hours between 2 datetime objects
     * Minutes are rounded to the nearest quarter
     *
     * @access public
     * @param  DateTime $d1
     * @param  DateTime $d2
     * @return float
     */
    public function getHours(DateTime $d1, DateTime $d2)
    {
        $seconds = $this->getRoundedSeconds(abs($d1->getTimestamp() - $d2->getTimestamp()));
        return round($seconds / 3600, 2);
    }

    /**
     * Round the timestamp to the nearest quarter
     *
     * @access public
     * @param  integer    $seconds   Timestamp
     * @return integer
     */
    public function getRoundedSeconds($seconds)
    {
        return (int) round($seconds / (15 * 60)) * (15 * 60);
    }

    /**
     * Return a timestamp if the given date format is correct otherwise return 0
     *
     * @access public
     * @param  string   $value  Date to parse
     * @param  string   $format Date format
     * @return integer
     */
    public function getValidDate($value, $format)
    {
        $date = DateTime::createFromFormat($format, $value);

        if ($date !== false) {
            $errors = DateTime::getLastErrors();
            if ($errors['error_count'] === 0 && $errors['warning_count'] === 0) {
                $timestamp = $date->getTimestamp();
                return $timestamp > 0 ? $timestamp : 0;
            }
        }

        return 0;
    }

    /**
     * Parse a date and return a unix timestamp, try different date formats
     *
     * @access public
     * @param  string   $value   Date to parse
     * @return integer
     */
    public function getTimestamp($value)
    {
        foreach ($this->getAllFormats() as $format) {
            $timestamp = $this->getValidDate($value, $format);

            if ($timestamp !== 0) {
                return $timestamp;
            }
        }

        return 0;
    }

    /**
     * Get ISO8601 date from user input
     *
     * @access public
     * @param  string   $value   Date to parse
     * @return string
     */
    public function getIsoDate($value)
    {
        return date('Y-m-d', ctype_digit($value) ? $value : $this->getTimestamp($value));
    }

    /**
     * Get all combinations of date/time formats
     *
     * @access public
     * @return string[]
     */
    public function getAllFormats()
    {
        $formats = array();

        foreach ($this->getDateFormats() as $date) {
            foreach ($this->getTimeFormats() as $time) {
                $formats[] = $date.' '.$time;
            }
        }

        return array_merge($formats, $this->getDateFormats());
    }

    /**
     * Return the list of supported date formats (for the parser)
     *
     * @access public
     * @return string[]
     */
    public function getDateFormats()
    {
        return array(
            $this->config->get('application_date_format', 'm/d/Y'),
            'Y-m-d',
            'Y_m_d',
        );
    }

    /**
     * Return the list of supported time formats (for the parser)
     *
     * @access public
     * @return string[]
     */
    public function getTimeFormats()
    {
        return array(
            'H:i',
            'g:i A',
            'g:iA',
        );
    }

    /**
     * Return the list of available date formats (for the config page)
     *
     * @access public
     * @return array
     */
    public function getAvailableFormats()
    {
        return array(
            'm/d/Y' => date('m/d/Y'),
            'd/m/Y' => date('d/m/Y'),
            'Y/m/d' => date('Y/m/d'),
            'd.m.Y' => date('d.m.Y'),
        );
    }

    /**
     * Remove the time from a timestamp
     *
     * @access public
     * @param  integer    $timestamp    Timestamp
     * @return integer
     */
    public function removeTimeFromTimestamp($timestamp)
    {
        return mktime(0, 0, 0, date('m', $timestamp), date('d', $timestamp), date('Y', $timestamp));
    }

    /**
     * Get a timetstamp from an ISO date format
     *
     * @access public
     * @param  string   $date
     * @return integer
     */
    public function getTimestampFromIsoFormat($date)
    {
        return $this->removeTimeFromTimestamp(ctype_digit($date) ? $date : strtotime($date));
    }

    /**
     * Format date (form display)
     *
     * @access public
     * @param  array    $values   Database values
     * @param  string[] $fields   Date fields
     * @param  string   $format   Date format
     */
    public function format(array &$values, array $fields, $format = '')
    {
        if ($format === '') {
            $format = $this->config->get('application_date_format');
        }

        foreach ($fields as $field) {
            if (! empty($values[$field])) {
                $values[$field] = date($format, $values[$field]);
            } else {
                $values[$field] = '';
            }
        }
    }

    /**
     * Convert date (form input data)
     *
     * @access public
     * @param  array    $values     Database values
     * @param  string[] $fields     Date fields
     * @param  boolean  $keep_time  Keep time or not
     */
    public function convert(array &$values, array $fields, $keep_time = false)
    {
        foreach ($fields as $field) {
            if (! empty($values[$field]) && ! is_numeric($values[$field])) {
                $timestamp = $this->getTimestamp($values[$field]);
                $values[$field] = $keep_time ? $timestamp : $this->removeTimeFromTimestamp($timestamp);
            }
        }
    }
}
