<?php

/*
 * This file is part of the eluceo/iCal package.
 *
 * (c) Markus Poerschke <markus@eluceo.de>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Eluceo\iCal\Component;

use Eluceo\iCal\Component;
use Eluceo\iCal\Property;
use Eluceo\iCal\Property\DateTimeProperty;
use Eluceo\iCal\Property\Event\Attendees;
use Eluceo\iCal\Property\Event\Organizer;
use Eluceo\iCal\Property\Event\RecurrenceRule;
use Eluceo\iCal\Property\Event\Description;
use Eluceo\iCal\PropertyBag;
use Eluceo\iCal\Property\Event\RecurrenceId;
use Eluceo\iCal\Property\DateTimesProperty;

/**
 * Implementation of the EVENT component.
 */
class Event extends Component
{
    const TIME_TRANSPARENCY_OPAQUE      = 'OPAQUE';
    const TIME_TRANSPARENCY_TRANSPARENT = 'TRANSPARENT';

    const STATUS_TENTATIVE = 'TENTATIVE';
    const STATUS_CONFIRMED = 'CONFIRMED';
    const STATUS_CANCELLED = 'CANCELLED';

    /**
     * @var string
     */
    protected $uniqueId;

    /**
     * The property indicates the date/time that the instance of
     * the iCalendar object was created.
     *
     * The value MUST be specified in the UTC time format.
     *
     * @var \DateTime
     */
    protected $dtStamp;

    /**
     * @var \DateTime
     */
    protected $dtStart;

    /**
     * Preferentially chosen over the duration if both are set.
     *
     * @var \DateTime
     */
    protected $dtEnd;

    /**
     * @var \DateInterval
     */
    protected $duration;

    /**
     * @var bool
     */
    protected $noTime = false;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $location;

    /**
     * @var string
     */
    protected $locationTitle;

    /**
     * @var string
     */
    protected $locationGeo;

    /**
     * @var string
     */
    protected $summary;

    /**
     * @var Organizer
     */
    protected $organizer;

    /**
     * @see http://www.ietf.org/rfc/rfc2445.txt 4.8.2.7 Time Transparency
     *
     * @var string
     */
    protected $transparency = self::TIME_TRANSPARENCY_OPAQUE;

    /**
     * If set to true the timezone will be added to the event.
     *
     * @var bool
     */
    protected $useTimezone = false;

    /**
     * @var int
     */
    protected $sequence = 0;

    /**
     * @var Attendees
     */
    protected $attendees;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $descriptionHTML;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var RecurrenceRule
     */
    protected $recurrenceRule;

    /**
     * This property specifies the date and time that the calendar
     * information was created.
     *
     * The value MUST be specified in the UTC time format.
     *
     * @var \DateTime
     */
    protected $created;

    /**
     * The property specifies the date and time that the information
     * associated with the calendar component was last revised.
     *
     * The value MUST be specified in the UTC time format.
     *
     * @var \DateTime
     */
    protected $modified;

    /**
     * Indicates if the UTC time should be used or not.
     *
     * @var bool
     */
    protected $useUtc = true;

    /**
     * @var bool
     */
    protected $cancelled;

    /**
     * This property is used to specify categories or subtypes
     * of the calendar component.  The categories are useful in searching
     * for a calendar component of a particular type and category.
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.8.1.2
     *
     * @var array
     */
    protected $categories;

    /**
     * https://tools.ietf.org/html/rfc5545#section-3.8.1.3.
     *
     * @var bool
     */
    protected $isPrivate = false;

    /**
     * Dates to be excluded from a series of events.
     *
     * @var \DateTime[]
     */
    protected $exDates = array();

    /**
     * @var RecurrenceId
     */
    protected $recurrenceId;

    public function __construct($uniqueId = null)
    {
        if (null == $uniqueId) {
            $uniqueId = uniqid();
        }

        $this->uniqueId = $uniqueId;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'VEVENT';
    }

    /**
     * {@inheritdoc}
     */
    public function buildPropertyBag()
    {
        $propertyBag = new PropertyBag();

        // mandatory information
        $propertyBag->set('UID', $this->uniqueId);

        $propertyBag->add(new DateTimeProperty('DTSTART', $this->dtStart, $this->noTime, $this->useTimezone, $this->useUtc));
        $propertyBag->set('SEQUENCE', $this->sequence);
        $propertyBag->set('TRANSP', $this->transparency);

        if ($this->status) {
            $propertyBag->set('STATUS', $this->status);
        }

        // An event can have a 'dtend' or 'duration', but not both.
        if (null != $this->dtEnd) {
            $propertyBag->add(new DateTimeProperty('DTEND', $this->dtEnd, $this->noTime, $this->useTimezone, $this->useUtc));
        } elseif (null != $this->duration) {
            $propertyBag->set('DURATION', $this->duration->format('P%dDT%hH%iM%sS'));
        }

        // optional information
        if (null != $this->url) {
            $propertyBag->set('URL', $this->url);
        }

        if (null != $this->location) {
            $propertyBag->set('LOCATION', $this->location);

            if (null != $this->locationGeo) {
                $propertyBag->add(
                    new Property(
                        'X-APPLE-STRUCTURED-LOCATION',
                        'geo:' . $this->locationGeo,
                        array(
                            'VALUE'          => 'URI',
                            'X-ADDRESS'      => $this->location,
                            'X-APPLE-RADIUS' => 49,
                            'X-TITLE'        => $this->locationTitle,
                        )
                    )
                );
                $propertyBag->set('GEO', str_replace(',', ';', $this->locationGeo));
            }
        }

        if (null != $this->summary) {
            $propertyBag->set('SUMMARY', $this->summary);
        }

        if (null != $this->attendees) {
            $propertyBag->add($this->attendees);
        }

        $propertyBag->set('CLASS', $this->isPrivate ? 'PRIVATE' : 'PUBLIC');

        if (null != $this->description) {
            $propertyBag->set('DESCRIPTION', new Description($this->description));
        }

        if (null != $this->descriptionHTML) {
            $propertyBag->add(
                new Property(
                    'X-ALT-DESC',
                    $this->descriptionHTML,
                    array(
                        'FMTTYPE' => 'text/html',
                    )
                )
            );
        }

        if (null != $this->recurrenceRule) {
            $propertyBag->set('RRULE', $this->recurrenceRule);
        }

        if (null != $this->recurrenceId) {
            $this->recurrenceId->applyTimeSettings($this->noTime, $this->useTimezone, $this->useUtc);
            $propertyBag->add($this->recurrenceId);
        }

        if (!empty($this->exDates)) {
            $propertyBag->add(new DateTimesProperty('EXDATE', $this->exDates, $this->noTime, $this->useTimezone, $this->useUtc));
        }

        if ($this->cancelled) {
            $propertyBag->set('STATUS', 'CANCELLED');
        }

        if (null != $this->organizer) {
            $propertyBag->add($this->organizer);
        }

        if ($this->noTime) {
            $propertyBag->set('X-MICROSOFT-CDO-ALLDAYEVENT', 'TRUE');
        }

        if (null != $this->categories) {
            $propertyBag->set('CATEGORIES', $this->categories);
        }

        $propertyBag->add(
            new DateTimeProperty('DTSTAMP', $this->dtStamp ?: new \DateTime(), false, false, true)
        );

        if ($this->created) {
            $propertyBag->add(new DateTimeProperty('CREATED', $this->created, false, false, true));
        }

        if ($this->modified) {
            $propertyBag->add(new DateTimeProperty('LAST-MODIFIED', $this->modified, false, false, true));
        }

        return $propertyBag;
    }

    /**
     * @param $dtEnd
     *
     * @return $this
     */
    public function setDtEnd($dtEnd)
    {
        $this->dtEnd = $dtEnd;

        return $this;
    }

    public function getDtEnd()
    {
        return $this->dtEnd;
    }

    public function setDtStart($dtStart)
    {
        $this->dtStart = $dtStart;

        return $this;
    }

    /**
     * @param $dtStamp
     *
     * @return $this
     */
    public function setDtStamp($dtStamp)
    {
        $this->dtStamp = $dtStamp;

        return $this;
    }

    /**
     * @param $duration
     *
     * @return $this
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @param        $location
     * @param string $title
     * @param null   $geo
     *
     * @return $this
     */
    public function setLocation($location, $title = '', $geo = null)
    {
        $this->location      = $location;
        $this->locationTitle = $title;
        $this->locationGeo   = $geo;

        return $this;
    }

    /**
     * @param $noTime
     *
     * @return $this
     */
    public function setNoTime($noTime)
    {
        $this->noTime = $noTime;

        return $this;
    }

    /**
     * @param int $sequence
     *
     * @return $this
     */
    public function setSequence($sequence)
    {
        $this->sequence = $sequence;

        return $this;
    }

    /**
     * @return int
     */
    public function getSequence()
    {
        return $this->sequence;
    }

    /**
     * @param Organizer $organizer
     *
     * @return $this
     */
    public function setOrganizer(Organizer $organizer)
    {
        $this->organizer = $organizer;

        return $this;
    }

    /**
     * @param $summary
     *
     * @return $this
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * @param $uniqueId
     *
     * @return $this
     */
    public function setUniqueId($uniqueId)
    {
        $this->uniqueId = $uniqueId;

        return $this;
    }

    /**
     * @return string
     */
    public function getUniqueId()
    {
        return $this->uniqueId;
    }

    /**
     * @param $url
     *
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @param $useTimezone
     *
     * @return $this
     */
    public function setUseTimezone($useTimezone)
    {
        $this->useTimezone = $useTimezone;

        return $this;
    }

    /**
     * @return bool
     */
    public function getUseTimezone()
    {
        return $this->useTimezone;
    }

    /**
     * @param Attendees $attendees
     *
     * @return $this
     */
    public function setAttendees(Attendees $attendees)
    {
        $this->attendees = $attendees;

        return $this;
    }

    /**
     * @param string $attendee
     * @param array  $params
     *
     * @return $this
     */
    public function addAttendee($attendee, $params = array())
    {
        if (!isset($this->attendees)) {
            $this->attendees = new Attendees();
        }
        $this->attendees->add($attendee, $params);

        return $this;
    }

    /**
     * @return Attendees
     */
    public function getAttendees()
    {
        return $this->attendees;
    }

    /**
     * @param $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @param $descriptionHTML
     *
     * @return $this
     */
    public function setDescriptionHTML($descriptionHTML)
    {
        $this->descriptionHTML = $descriptionHTML;

        return $this;
    }

    /**
     * @param bool $useUtc
     *
     * @return $this
     */
    public function setUseUtc($useUtc = true)
    {
        $this->useUtc = $useUtc;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getDescriptionHTML()
    {
        return $this->descriptionHTML;
    }

    /**
     * @param $status
     *
     * @return $this
     */
    public function setCancelled($status)
    {
        $this->cancelled = (bool) $status;

        return $this;
    }

    /**
     * @param $transparency
     *
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function setTimeTransparency($transparency)
    {
        $transparency = strtoupper($transparency);
        if ($transparency === self::TIME_TRANSPARENCY_OPAQUE
            || $transparency === self::TIME_TRANSPARENCY_TRANSPARENT
        ) {
            $this->transparency = $transparency;
        } else {
            throw new \InvalidArgumentException('Invalid value for transparancy');
        }

        return $this;
    }

    /**
     * @param $status
     *
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function setStatus($status)
    {
        $status = strtoupper($status);
        if ($status == self::STATUS_CANCELLED
            || $status == self::STATUS_CONFIRMED
            || $status == self::STATUS_TENTATIVE
        ) {
            $this->status = $status;
        } else {
            throw new \InvalidArgumentException('Invalid value for status');
        }

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
     * @return RecurrenceRule
     */
    public function getRecurrenceRule()
    {
        return $this->recurrenceRule;
    }

    /**
     * @param $dtStamp
     *
     * @return $this
     */
    public function setCreated($dtStamp)
    {
        $this->created = $dtStamp;

        return $this;
    }

    /**
     * @param $dtStamp
     *
     * @return $this
     */
    public function setModified($dtStamp)
    {
        $this->modified = $dtStamp;

        return $this;
    }

    /**
     * @param $categories
     *
     * @return $this
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * Sets the event privacy.
     *
     * @param bool $flag
     *
     * @return $this
     */
    public function setIsPrivate($flag)
    {
        $this->isPrivate = (bool) $flag;

        return $this;
    }

    /**
     * @param \DateTime $dateTime
     *
     * @return \Eluceo\iCal\Component\Event
     */
    public function addExDate(\DateTime $dateTime)
    {
        $this->exDates[] = $dateTime;

        return $this;
    }

    /**
     * @return \DateTime[]
     */
    public function getExDates()
    {
        return $this->exDates;
    }

    /**
     * @param \DateTime[]
     *
     * @return \Eluceo\iCal\Component\Event
     */
    public function setExDates(array $exDates)
    {
        $this->exDates = $exDates;

        return $this;
    }

    /**
     * @return \Eluceo\iCal\Property\Event\RecurrenceId
     */
    public function getRecurrenceId()
    {
        return $this->recurrenceId;
    }

    /**
     * @param RecurrenceId $recurrenceId
     *
     * @return \Eluceo\iCal\Component\Event
     */
    public function setRecurrenceId(RecurrenceId $recurrenceId)
    {
        $this->recurrenceId = $recurrenceId;

        return $this;
    }
}
