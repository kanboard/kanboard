<?php

/*
 * This file is part of the eluceo/iCal package.
 *
 * (c) Markus Poerschke <markus@eluceo.de>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Eluceo\iCal\Property;

use Eluceo\iCal\Property;
use Eluceo\iCal\Util\DateUtil;

class DateTimesProperty extends Property
{
    /**
     * @param string               $name
     * @param \DateTimeInterface[] $dateTimes
     * @param bool                 $noTime
     * @param bool                 $useTimezone
     * @param bool                 $useUtc
     * @param string               $timezoneString
     */
    public function __construct(
        $name,
        $dateTimes = [],
        $noTime = false,
        $useTimezone = false,
        $useUtc = false,
        $timezoneString = ''
    ) {
        $dates = [];
        $dateTime = new \DateTimeImmutable();
        foreach ($dateTimes as $dateTime) {
            $dates[] = DateUtil::getDateString($dateTime, $noTime, $useTimezone, $useUtc);
        }

        //@todo stop this triggering an E_NOTICE when $dateTimes is empty
        $params = DateUtil::getDefaultParams($dateTime, $noTime, $useTimezone, $timezoneString);

        parent::__construct($name, $dates, $params);
    }
}
