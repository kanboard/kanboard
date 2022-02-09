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

use Eluceo\iCal\ParameterBag;
use Eluceo\iCal\Property\ValueInterface;
use InvalidArgumentException;

/**
 * Implementation of Recurrence Rule.
 *
 * @see https://tools.ietf.org/html/rfc5545#section-3.8.5.3
 */
class RecurrenceRule implements ValueInterface
{
    const FREQ_YEARLY = 'YEARLY';
    const FREQ_MONTHLY = 'MONTHLY';
    const FREQ_WEEKLY = 'WEEKLY';
    const FREQ_DAILY = 'DAILY';
    const FREQ_HOURLY = 'HOURLY';
    const FREQ_MINUTELY = 'MINUTELY';
    const FREQ_SECONDLY = 'SECONDLY';

    const WEEKDAY_SUNDAY = 'SU';
    const WEEKDAY_MONDAY = 'MO';
    const WEEKDAY_TUESDAY = 'TU';
    const WEEKDAY_WEDNESDAY = 'WE';
    const WEEKDAY_THURSDAY = 'TH';
    const WEEKDAY_FRIDAY = 'FR';
    const WEEKDAY_SATURDAY = 'SA';

    /**
     * The frequency of an Event.
     *
     * @var string
     */
    protected $freq = self::FREQ_YEARLY;

    /**
     * BYSETPOS must require use of other BY*.
     *
     * @var bool
     */
    protected $canUseBySetPos = false;

    /**
     * @var int|null
     */
    protected $interval = 1;

    /**
     * @var int|null
     */
    protected $count = null;

    /**
     * @var \DateTimeInterface|null
     */
    protected $until = null;

    /**
     * @var string|null
     */
    protected $wkst;

    /**
     * @var array|null
     */
    protected $bySetPos = null;

    /**
     * @var string|null
     */
    protected $byMonth;

    /**
     * @var string|null
     */
    protected $byWeekNo;

    /**
     * @var string|null
     */
    protected $byYearDay;

    /**
     * @var string|null
     */
    protected $byMonthDay;

    /**
     * @var string|null
     */
    protected $byDay;

    /**
     * @var string|null
     */
    protected $byHour;

    /**
     * @var string|null
     */
    protected $byMinute;

    /**
     * @var string|null
     */
    protected $bySecond;

    public function getEscapedValue(): string
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

        if (null !== $this->bySetPos && $this->canUseBySetPos) {
            $parameterBag->setParam('BYSETPOS', $this->bySetPos);
        }

        if (null !== $this->byMonth) {
            $parameterBag->setParam('BYMONTH', explode(',', $this->byMonth));
        }

        if (null !== $this->byWeekNo) {
            $parameterBag->setParam('BYWEEKNO', explode(',', $this->byWeekNo));
        }

        if (null !== $this->byYearDay) {
            $parameterBag->setParam('BYYEARDAY', explode(',', $this->byYearDay));
        }

        if (null !== $this->byMonthDay) {
            $parameterBag->setParam('BYMONTHDAY', explode(',', $this->byMonthDay));
        }

        if (null !== $this->byDay) {
            $parameterBag->setParam('BYDAY', explode(',', $this->byDay));
        }

        if (null !== $this->byHour) {
            $parameterBag->setParam('BYHOUR', explode(',', $this->byHour));
        }

        if (null !== $this->byMinute) {
            $parameterBag->setParam('BYMINUTE', explode(',', $this->byMinute));
        }

        if (null !== $this->bySecond) {
            $parameterBag->setParam('BYSECOND', explode(',', $this->bySecond));
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
     * @return $this
     */
    public function setUntil(\DateTimeInterface $until = null)
    {
        $this->until = $until;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
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
        if (@constant('static::FREQ_' . $freq) !== null) {
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
     * The BYSETPOS filters one interval of events by the specified position.
     * A positive position will start from the beginning and go forward while
     * a negative position will start at the end and move backward.
     *
     * Valid values are a comma separated string or an array of integers
     * from 1 to 366 or negative integers from -1 to -366.
     *
     * @param int|string|array|null $value
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function setBySetPos($value)
    {
        if (null === $value) {
            $this->bySetPos = $value;

            return $this;
        }

        if (!(is_string($value) || is_array($value) || is_int($value))) {
            throw new InvalidArgumentException('Invalid value for BYSETPOS');
        }

        $list = $value;

        if (is_int($value)) {
            if ($value === 0 || $value < -366 || $value > 366) {
                throw new InvalidArgumentException('Invalid value for BYSETPOS');
            }
            $this->bySetPos = [$value];

            return $this;
        }

        if (is_string($value)) {
            $list = explode(',', $value);
        }

        $output = [];

        foreach ($list as $item) {
            if (is_string($item)) {
                if (!preg_match('/^ *-?[0-9]* *$/', $item)) {
                    throw new InvalidArgumentException('Invalid value for BYSETPOS');
                }
                $item = intval($item);
            }

            if (!is_int($item) || $item === 0 || $item < -366 || $item > 366) {
                throw new InvalidArgumentException('Invalid value for BYSETPOS');
            }

            $output[] = $item;
        }

        $this->bySetPos = $output;

        return $this;
    }

    /**
     * The BYMONTH rule part specifies a COMMA-separated list of months of the year.
     * Valid values are 1 to 12.
     *
     * @param int $month
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function setByMonth($month)
    {
        if (!is_integer($month) || $month <= 0 || $month > 12) {
            throw new InvalidArgumentException('Invalid value for BYMONTH');
        }

        $this->byMonth = $month;

        $this->canUseBySetPos = true;

        return $this;
    }

    /**
     * The BYWEEKNO rule part specifies a COMMA-separated list of ordinals specifying weeks of the year.
     * Valid values are 1 to 53 or -53 to -1.
     *
     * @param int $value
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function setByWeekNo($value)
    {
        if (!is_integer($value) || $value > 53 || $value < -53 || $value === 0) {
            throw new InvalidArgumentException('Invalid value for BYWEEKNO');
        }

        $this->byWeekNo = $value;

        $this->canUseBySetPos = true;

        return $this;
    }

    /**
     * The BYYEARDAY rule part specifies a COMMA-separated list of days of the year.
     * Valid values are 1 to 366 or -366 to -1.
     *
     * @param int $day
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function setByYearDay($day)
    {
        if (!is_integer($day) || $day > 366 || $day < -366 || $day === 0) {
            throw new InvalidArgumentException('Invalid value for BYYEARDAY');
        }

        $this->byYearDay = $day;

        $this->canUseBySetPos = true;

        return $this;
    }

    /**
     * The BYMONTHDAY rule part specifies a COMMA-separated list of days of the month.
     * Valid values are 1 to 31 or -31 to -1.
     *
     * @param int $day
     *
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function setByMonthDay($day)
    {
        if (!is_integer($day) || $day > 31 || $day < -31 || $day === 0) {
            throw new InvalidArgumentException('Invalid value for BYMONTHDAY');
        }

        $this->byMonthDay = $day;

        $this->canUseBySetPos = true;

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
     * @return $this
     */
    public function setByDay(string $day)
    {
        $this->byDay = $day;

        $this->canUseBySetPos = true;

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

        $this->canUseBySetPos = true;

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

        $this->canUseBySetPos = true;

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

        $this->canUseBySetPos = true;

        return $this;
    }
}
