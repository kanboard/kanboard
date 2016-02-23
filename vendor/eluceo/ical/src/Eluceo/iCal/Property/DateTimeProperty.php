<?php

namespace Eluceo\iCal\Property;

use Eluceo\iCal\Property;

class DateTimeProperty extends Property
{
    /**
     * @param string    $name
     * @param \DateTime $dateTime
     * @param bool      $noTime
     * @param bool      $useTimezone
     * @param bool      $useUtc
     */
    function __construct(
        $name,
        \DateTime $dateTime = null,
        $noTime = false,
        $useTimezone = false,
        $useUtc = false
    )
    {
        $dateString = $this->getDateString($dateTime, $noTime, $useTimezone, $useUtc);
        $params     = array();

        if ($useTimezone) {
            $timeZone       = $dateTime->getTimezone()->getName();
            $params['TZID'] = $timeZone;
        }

        if ($noTime) {
            $params['VALUE'] = 'DATE';
        }

        parent::__construct($name, $dateString, $params);
    }

    /**
     * Returns a formatted date string.
     *
     * @param \DateTime|null $dateTime The DateTime object
     * @param bool           $noTime Indicates if the time will be added
     * @param bool           $useTimezone
     * @param bool           $useUtc
     *
     * @return mixed
     */
    private function getDateString(\DateTime $dateTime = null, $noTime = false, $useTimezone = false, $useUtc = false)
    {
        if (empty($dateTime)) {
            $dateTime = new \DateTime();
        }

        return $dateTime->format($this->getDateFormat($noTime, $useTimezone, $useUtc));
    }

    /**
     * Returns the date format that can be passed to DateTime::format().
     *
     * @param bool $noTime Indicates if the time will be added
     * @param bool $useTimezone
     * @param bool $useUtc
     *
     * @return string
     */
    private function getDateFormat($noTime = false, $useTimezone = false, $useUtc = false)
    {
        // Do not use UTC time (Z) if timezone support is enabled.
        if ($useTimezone || !$useUtc) {
            return $noTime ? 'Ymd' : 'Ymd\THis';
        }

        return $noTime ? 'Ymd' : 'Ymd\THis\Z';
    }
}
