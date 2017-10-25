<?php

namespace PicoFeed\Parser;

use DateTime;
use DateTimeZone;
use PicoFeed\Base;

/**
 * Date Parser.
 *
 * @package PicoFeed\Parser
 * @author  Frederic Guillot
 */
class DateParser extends Base
{
    /**
     * Timezone used to parse feed dates.
     *
     * @access private
     * @var string
     */
    private $timezone = 'UTC';

    /**
     * Supported formats [ 'format' => length ].
     *
     * @var array
     */
    public $formats = array(
        DATE_ATOM => null,
        DATE_RSS => null,
        DATE_COOKIE => null,
        DATE_ISO8601 => null,
        DATE_RFC822 => null,
        DATE_RFC850 => null,
        DATE_RFC1036 => null,
        DATE_RFC1123 => null,
        DATE_RFC2822 => null,
        DATE_RFC3339 => null,
        'l, d M Y H:i:s' => null,
        'D, d M Y H:i:s' => 25,
        'D, d M Y h:i:s' => 25,
        'D M d Y H:i:s' => 24,
        'j M Y H:i:s' => 20,
        'Y-m-d H:i:s' => 19,
        'Y-m-d\TH:i:s' => 19,
        'd/m/Y H:i:s' => 19,
        'D, d M Y' => 16,
        'Y-m-d' => 10,
        'd-m-Y' => 10,
        'm-d-Y' => 10,
        'd.m.Y' => 10,
        'm.d.Y' => 10,
        'd/m/Y' => 10,
        'm/d/Y' => 10,
    );

    /**
     * Try to parse all date format for broken feeds.
     *
     * @param string $value Original date format
     *
     * @return DateTime
     */
    public function getDateTime($value)
    {
        $value = trim($value);

        foreach ($this->formats as $format => $length) {
            $truncated_value = $value;
            if ($length !== null) {
                $truncated_value = substr($truncated_value, 0, $length);
            }

            $date = $this->getValidDate($format, $truncated_value);
            if ($date !== false) {
                return $date;
            }
        }

        return $this->getCurrentDateTime();
    }

    /**
     * Get a valid date from a given format.
     *
     * @param string $format Date format
     * @param string $value  Original date value
     *
     * @return DateTime|bool
     */
    public function getValidDate($format, $value)
    {
        $date = DateTime::createFromFormat($format, $value, $this->getTimeZone());

        if ($date !== false) {
            $errors = DateTime::getLastErrors();

            if ($errors['error_count'] === 0 && $errors['warning_count'] === 0) {
                return $date;
            }
        }

        return false;
    }

    /**
     * Get the current datetime.
     *
     * @return DateTime
     */
    public function getCurrentDateTime()
    {
        return new DateTime('now', $this->getTimeZone());
    }

    /**
     * Get DateTimeZone instance
     *
     * @access public
     * @return DateTimeZone
     */
    public function getTimeZone()
    {
        return new DateTimeZone($this->config->getTimezone() ?: $this->timezone);
    }
}
