<?php

/*
 * This file is part of the eluceo/iCal package.
 *
 * (c) Markus Poerschke <markus@eluceo.de>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Eluceo\iCal\Property\Event;

use Eluceo\iCal\Property\ValueInterface;
use Eluceo\iCal\ParameterBag;
use InvalidArgumentException;

/**
 * Implementation of Recurrence Rule.
 *
 * @see http://www.ietf.org/rfc/rfc2445.txt 3.3.10.  Recurrence Rule
 */
class RecurrenceRule implements ValueInterface
{
    const FREQ_YEARLY  = 'YEARLY';
    const FREQ_MONTHLY = 'MONTHLY';
    const FREQ_WEEKLY  = 'WEEKLY';
    const FREQ_DAILY   = 'DAILY';

    const WEEKDAY_SUNDAY    = 'SU';
    const WEEKDAY_MONDAY    = 'MO';
    const WEEKDAY_TUESDAY   = 'TU';
    const WEEKDAY_WEDNESDAY = 'WE';
    const WEEKDAY_THURSDAY  = 'TH';
    const WEEKDAY_FRIDAY    = 'FR';
    const WEEKDAY_SATURDAY  = 'SA';

    /**
     * The frequency of an Event.
     *
     * @var string
     */
    protected $freq = self::FREQ_YEARLY;

    /**
     * @var null|int
     */
    protected $interval = 1;

    /**
     * @var null|int
     */
    protected $count = null;

    /**
     * @var null|\DateTime
     */
    protected $until = null;

    /**
     * @var null|string
     */
    protected $wkst;

    /**
     * @var null|string
     */
    protected $byMonth;

    /**
     * @var null|string
     */
    protected $byWeekNo;

    /**
     * @var null|string
     */
    protected $byYearDay;

    /**
     * @var null|string
     */
    protected $byMonthDay;

    /**
     * @var null|string
     */
    protected $byDay;

    /**
     * @var null|string
     */
    protected $byHour;

    /**
     * @var null|string
     */
    protected $byMinute;

    /**
     * @var null|string
     */
    protected $bySecond;

    /**
     * Return the value of the Property as an escaped string.
     *
     * Escape values as per RFC 2445. See http://www.kanzaki.com/docs/ical/text.html
     *
     * @return string
     */
    public function getEscapedValue()
    {
        return $this->buildParameterBag()->toString();
    }

    /**
     * @return ParameterBag
     */
    protected function buildParameterBag()
    {
        $parameterBag = new ParameterBag();

        $parameterBag->setParam('FREQ', $this->freq);

        if (null !== $this->interval) {
            $parameterBag->setParam('INTERVAL', $this->interval);
        }

        if (null !== $this->count) {
            $parameterBag->setParam('COUNT', $this->count);
        }

        if (null != $this->until) {
            $parameterBag->setParam('UNTIL', $this->until->format('Ymd\THis\Z'));
        }

        if (null !== $this->wkst) {
            $parameterBag->setParam('WKST', $this->wkst);
        }

        if (null !== $this->byMonth) {
            $parameterBag->setParam('BYMONTH', $this->byMonth);
        }

        if (null !== $this->byWeekNo) {
            $parameterBag->setParam('BYWEEKNO', $this->byWeekNo);
        }

        if (null !== $this->byYearDay) {
            $parameterBag->setParam('BYYEARDAY', $this->byYearDay);
        }

        if (null !== $this->byMonthDay) {
            $parameterBag->setParam('BYMONTHDAY', $this->byMonthDay);
        }

        if (null !== $this->byDay) {
            $parameterBag->setParam('BYDAY', $this->byDay);
        }

        if (null !== $this->byHour) {
            $parameterBag->setParam('BYHOUR', $this->byHour);
        }

        if (null !== $this->byMinute) {
            $parameterBag->setParam('BYMINUTE', $this->byMinute);
        }

        if (null !== $this->bySecond) {
            $parameterBag->setParam('BYSECOND', $this->bySecond);
        }

        return $parameterBag;
    }

    /**
     * @param int|null $count
     *
     * @return $this
     */
    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param \DateTime|null $until
     *
     * @return $this
     */
    public function setUntil(\DateTime $until = null)
    {
        $this->until = $until;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getUntil()
    {
        return $this->until;
    }

    /**
     * The FREQ rule part identifies the type of recurrence rule.  This
     * rule part MUST be specified in the recurrence rule.  Valid values
     * include.
     *
     * SECONDLY, to specify repeating events based on an interval of a second or more;
     * MINUTELY, to specify repeating events based on an interval of a minute or more;
     * HOURLY, to specify repeating events based on an interval of an hour or more;
     * DAILY, to specify repeating events based on an interval of a day or more;
     * WEEKLY, to specify repeating events based on an interval of a week or more;
     * MONTHLY, to specify repeating events based on an interval of a month or more;
     * YEARLY, to specify repeating events based on an interval of a year or more.
     *
     * @param string $freq
     *
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function setFreq($freq)
    {
        if (self::FREQ_YEARLY === $freq || self::FREQ_MONTHLY === $freq
            || self::FREQ_WEEKLY === $freq
            || self::FREQ_DAILY === $freq
        ) {
            $this->freq = $freq;
        } else {
            throw new \InvalidArgumentException("The Frequency {$freq} is not supported.");
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getFreq()
    {
        return $this->freq;
    }

    /**
     * The INTERVAL rule part contains a positive integer representing at
     * which intervals the recurrence rule repeats.
     *
     * @param int|null $interval
     *
     * @return $this
     */
    public function setInterval($interval)
    {
        $this->interval = $interval;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getInterval()
    {
        return $this->interval;
    }

    /**
     * The WKST rule part specifies the day on which the workweek starts.
     * Valid values are MO, TU, WE, TH, FR, SA, and SU.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setWkst($value)
    {
        $this->wkst = $value;

        return $this;
    }

    /**
     * The BYMONTH rule part specifies a COMMA-separated list of months of the year.
     * Valid values are 1 to 12.
     *
     * @param int $month
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function setByMonth($month)
    {
        if (!is_integer($month) || $month < 0 || $month > 12) {
            throw new InvalidArgumentException('Invalid value for BYMONTH');
        }

        $this->byMonth = $month;

        return $this;
    }

    /**
     * The BYWEEKNO rule part specifies a COMMA-separated list of ordinals specifying weeks of the year.
     * Valid values are 1 to 53 or -53 to -1.
     *
     * @param int $value
     *
     * @return $this
     */
    public function setByWeekNo($value)
    {
        $this->byWeekNo = $value;

        return $this;
    }

    /**
     * The BYYEARDAY rule part specifies a COMMA-separated list of days of the year.
     * Valid values are 1 to 366 or -366 to -1.
     *
     * @param int $day
     *
     * @return $this
     */
    public function setByYearDay($day)
    {
        $this->byYearDay = $day;

        return $this;
    }

    /**
     * The BYMONTHDAY rule part specifies a COMMA-separated list of days of the month.
     * Valid values are 1 to 31 or -31 to -1.
     *
     * @param int $day
     *
     * @return $this
     */
    public function setByMonthDay($day)
    {
        $this->byMonthDay = $day;

        return $this;
    }

    /**
     * The BYDAY rule part specifies a COMMA-separated list of days of the week;.
     *
     * SU indicates Sunday; MO indicates Monday; TU indicates Tuesday;
     * WE indicates Wednesday; TH indicates Thursday; FR indicates Friday; and SA indicates Saturday.
     *
     * Each BYDAY value can also be preceded by a positive (+n) or negative (-n) integer.
     * If present, this indicates the nth occurrence of a specific day within the MONTHLY or YEARLY "RRULE".
     *
     * @param string $day
     *
     * @return $this
     */
    public function setByDay($day)
    {
        $this->byDay = $day;

        return $this;
    }

    /**
     * The BYHOUR rule part specifies a COMMA-separated list of hours of the day.
     * Valid values are 0 to 23.
     *
     * @param int $value
     *
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function setByHour($value)
    {
        if (!is_integer($value) || $value < 0 || $value > 23) {
            throw new \InvalidArgumentException('Invalid value for BYHOUR');
        }

        $this->byHour = $value;

        return $this;
    }

    /**
     * The BYMINUTE rule part specifies a COMMA-separated list of minutes within an hour.
     * Valid values are 0 to 59.
     *
     * @param int $value
     *
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function setByMinute($value)
    {
        if (!is_integer($value) || $value < 0 || $value > 59) {
            throw new \InvalidArgumentException('Invalid value for BYMINUTE');
        }

        $this->byMinute = $value;

        return $this;
    }

    /**
     * The BYSECOND rule part specifies a COMMA-separated list of seconds within a minute.
     * Valid values are 0 to 60.
     *
     * @param int $value
     *
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function setBySecond($value)
    {
        if (!is_integer($value) || $value < 0 || $value > 60) {
            throw new \InvalidArgumentException('Invalid value for BYSECOND');
        }

        $this->bySecond = $value;

        return $this;
    }
}
