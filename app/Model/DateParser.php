<?php

namespace Model;

use DateTime;

/**
 * Date parser model
 *
 * @package  model
 * @author   Frederic Guillot
 */
class DateParser extends Base
{
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
     * Parse a date ad return a unix timestamp, try different date formats
     *
     * @access public
     * @param  string   $value   Date to parse
     * @return integer
     */
    public function getTimestamp($value)
    {
        foreach ($this->getDateFormats() as $format) {

            $timestamp = $this->getValidDate($value, $format);

            if ($timestamp !== 0) {
                return $timestamp;
            }
        }

        return 0;
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
        );
    }

    /**
     * For a given timestamp, reset the date to midnight
     *
     * @access public
     * @param  integer    $timestamp    Timestamp
     * @return integer
     */
    public function resetDateToMidnight($timestamp)
    {
        return mktime(0, 0, 0, date('m', $timestamp), date('d', $timestamp), date('Y', $timestamp));
    }

    /**
     * Get a timetstamp from an ISO date format
     *
     * @access public
     * @param  string   $date   Date format
     * @return integer
     */
    public function getTimestampFromIsoFormat($date)
    {
        return $this->resetDateToMidnight(strtotime($date));
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
            }
            else {
                $values[$field] = '';
            }
        }
    }

    /**
     * Convert date (form input data)
     *
     * @access public
     * @param  array    $values   Database values
     * @param  string[] $fields   Date fields
     */
    public function convert(array &$values, array $fields)
    {
        foreach ($fields as $field) {

            if (! empty($values[$field]) && ! is_numeric($values[$field])) {
                $values[$field] = $this->getTimestamp($values[$field]);
            }
        }
    }
}
