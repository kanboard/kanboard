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
    public static function getDefaultParams(\DateTime $dateTime = null, $noTime = false, $useTimezone = false)
    {
        $params = array();

        if ($useTimezone) {
            $timeZone       = $dateTime->getTimezone()->getName();
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
     * @param \DateTime|null $dateTime    The DateTime object
     * @param bool           $noTime      Indicates if the time will be added
     * @param bool           $useTimezone
     * @param bool           $useUtc
     *
     * @return mixed
     */
    public static function getDateString(\DateTime $dateTime = null, $noTime = false, $useTimezone = false, $useUtc = false)
    {
        if (empty($dateTime)) {
            $dateTime = new \DateTime();
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
