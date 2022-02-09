<?php

/*
 * This file is part of the eluceo/iCal package.
 *
 * (c) Markus Poerschke <markus@eluceo.de>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Eluceo\iCal\Util;

class DateUtil
{
    public static function getDefaultParams(\DateTimeInterface $dateTime = null, $noTime = false, $useTimezone = false, $timezoneString = '')
    {
        $params = [];

        if ($useTimezone && $noTime === false) {
            $timeZone = $timezoneString === '' ? $dateTime->getTimezone()->getName() : $timezoneString;
            $params['TZID'] = $timeZone;
        }

        if ($noTime) {
            $params['VALUE'] = 'DATE';
        }

        return $params;
    }

    /**
     * Returns a formatted date string.
     *
     * @param \DateTimeInterface|null $dateTime    The DateTime object
     * @param bool                    $noTime      Indicates if the time will be added
     * @param bool                    $useTimezone
     * @param bool                    $useUtc
     *
     * @return mixed
     */
    public static function getDateString(\DateTimeInterface $dateTime = null, $noTime = false, $useTimezone = false, $useUtc = false)
    {
        if (empty($dateTime)) {
            $dateTime = new \DateTimeImmutable();
        }

        // Only convert the DateTime to UTC if there is a time present. For date-only the
        // timezone is meaningless and converting it might shift it to the wrong date.
        // Do not convert DateTime to UTC if a timezone it specified, as it should be local time.
        if (!$noTime && $useUtc && !$useTimezone) {
            $dateTime = clone $dateTime;
            $dateTime = $dateTime->setTimezone(new \DateTimeZone('UTC'));
        }

        return $dateTime->format(self::getDateFormat($noTime, $useTimezone, $useUtc));
    }

    /**
     * Returns the date format that can be passed to DateTime::format().
     *
     * @param bool $noTime      Indicates if the time will be added
     * @param bool $useTimezone
     * @param bool $useUtc
     *
     * @return string
     */
    public static function getDateFormat($noTime = false, $useTimezone = false, $useUtc = false)
    {
        // Do not use UTC time (Z) if timezone support is enabled.
        if ($useTimezone || !$useUtc) {
            return $noTime ? 'Ymd' : 'Ymd\THis';
        }

        return $noTime ? 'Ymd' : 'Ymd\THis\Z';
    }
}
