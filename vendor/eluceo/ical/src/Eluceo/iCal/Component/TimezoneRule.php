<?php

/*
 * This file is part of the eluceo/iCal package.
 *
 * (c) Ronny Unger <ronny.unger@gmx.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eluceo\iCal\Component;

use Eluceo\iCal\Component;
use Eluceo\iCal\PropertyBag;
use Eluceo\iCal\Property\Event\RecurrenceRule;

/**
 * Implementation of Standard Time and Daylight Saving Time observances (or rules)
 * which define the TIMEZONE component.
 */
class TimezoneRule extends Component
{
    const TYPE_DAYLIGHT = 'DAYLIGHT';
    const TYPE_STANDARD = 'STANDARD';

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $tzOffsetFrom;

    /**
     * @var string
     */
    protected $tzOffsetTo;

    /**
     * @var string
     */
    protected $tzName;

    /**
     * @var \DateTime
     */
    protected $dtStart;

    /**
     * @var RecurrenceRule
     */
    protected $recurrenceRule;

    /**
     * create new Timezone Rule object by giving a rule type identifier.
     *
     * @param string $ruleType one of DAYLIGHT or STANDARD
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($ruleType)
    {
        $ruleType = strtoupper($ruleType);
        if ($ruleType === self::TYPE_DAYLIGHT || $ruleType === self::TYPE_STANDARD) {
            $this->type = $ruleType;
        } else {
            throw new \InvalidArgumentException('Invalid value for timezone rule type');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildPropertyBag()
    {
        $propertyBag = new PropertyBag();

        if (null != $this->getTzName()) {
            $propertyBag->set('TZNAME', $this->getTzName());
        }

        if (null != $this->getTzOffsetFrom()) {
            $propertyBag->set('TZOFFSETFROM', $this->getTzOffsetFrom());
        }

        if (null != $this->getTzOffsetTo()) {
            $propertyBag->set('TZOFFSETTO', $this->getTzOffsetTo());
        }

        if (null != $this->getDtStart()) {
            $propertyBag->set('DTSTART', $this->getDtStart());
        }

        if (null != $this->recurrenceRule) {
            $propertyBag->set('RRULE', $this->recurrenceRule);
        }

        return $propertyBag;
    }

    /**
     * @param $offset
     *
     * @return $this
     */
    public function setTzOffsetFrom($offset)
    {
        $this->tzOffsetFrom = $offset;

        return $this;
    }

    /**
     * @param $offset
     *
     * @return $this
     */
    public function setTzOffsetTo($offset)
    {
        $this->tzOffsetTo = $offset;

        return $this;
    }

    /**
     * @param $name
     *
     * @return $this
     */
    public function setTzName($name)
    {
        $this->tzName = $name;

        return $this;
    }

    /**
     * @param \DateTime $dtStart
     *
     * @return $this
     */
    public function setDtStart(\DateTime $dtStart)
    {
        $this->dtStart = $dtStart;

        return $this;
    }

    /**
     * @param RecurrenceRule $recurrenceRule
     *
     * @return $this
     */
    public function setRecurrenceRule(RecurrenceRule $recurrenceRule)
    {
        $this->recurrenceRule = $recurrenceRule;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getTzOffsetFrom()
    {
        return $this->tzOffsetFrom;
    }

    /**
     * @return string
     */
    public function getTzOffsetTo()
    {
        return $this->tzOffsetTo;
    }

    /**
     * @return string
     */
    public function getTzName()
    {
        return $this->tzName;
    }

    /**
     * @return RecurrenceRule
     */
    public function getRecurrenceRule()
    {
        return $this->recurrenceRule;
    }

    /**
     * @return mixed return string representation of start date or NULL if no date was given
     */
    public function getDtStart()
    {
        if ($this->dtStart) {
            return $this->dtStart->format('Ymd\THis');
        }
    }
}
