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
use Eluceo\iCal\Property;
use Eluceo\iCal\Property\ValueInterface;
use Eluceo\iCal\Util\DateUtil;

/**
 * Implementation of Recurrence Id.
 *
 * @see https://tools.ietf.org/html/rfc5545#section-3.8.4.4
 */
class RecurrenceId extends Property
{
    /**
     * The effective range of recurrence instances from the instance
     * specified by the recurrence identifier specified by the property.
     */
    const RANGE_THISANDPRIOR = 'THISANDPRIOR';
    const RANGE_THISANDFUTURE = 'THISANDFUTURE';

    /**
     * The dateTime to identify a particular instance of a recurring event which is getting modified.
     *
     * @var \DateTimeInterface
     */
    protected $dateTime;

    /**
     * Specify the effective range of recurrence instances from the instance.
     *
     * @var string
     */
    protected $range;

    public function __construct(\DateTimeInterface $dateTime = null)
    {
        $this->name = 'RECURRENCE-ID';
        $this->parameterBag = new ParameterBag();
        if (isset($dateTime)) {
            $this->dateTime = $dateTime;
        }
    }

    public function applyTimeSettings($noTime = false, $useTimezone = false, $useUtc = false, $timezoneString = '')
    {
        $params = DateUtil::getDefaultParams($this->dateTime, $noTime, $useTimezone, $timezoneString);
        foreach ($params as $name => $value) {
            $this->parameterBag->setParam($name, $value);
        }

        if ($this->range) {
            $this->parameterBag->setParam('RANGE', $this->range);
        }

        $this->setValue(DateUtil::getDateString($this->dateTime, $noTime, $useTimezone, $useUtc));
    }

    /**
     * @return \DateTimeInterface
     */
    public function getDatetime()
    {
        return $this->dateTime;
    }

    /**
     * @return \Eluceo\iCal\Property\Event\RecurrenceId
     */
    public function setDatetime(\DateTimeInterface $dateTime)
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    /**
     * @return string
     */
    public function getRange()
    {
        return $this->range;
    }

    /**
     * @param string $range
     *
     * @return \Eluceo\iCal\Property\Event\RecurrenceId
     */
    public function setRange($range)
    {
        $this->range = $range;

        return $this;
    }

    /**
     * Get all unfolded lines.
     *
     * @return array
     */
    public function toLines()
    {
        if (!$this->value instanceof ValueInterface) {
            throw new \Exception('The value must implement the ValueInterface. Call RecurrenceId::applyTimeSettings() before adding RecurrenceId.');
        } else {
            return parent::toLines();
        }
    }
}
