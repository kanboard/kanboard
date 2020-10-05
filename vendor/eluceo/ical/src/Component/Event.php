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
use Eluceo\iCal\Property\DateTimesProperty;
use Eluceo\iCal\Property\Event\Attachment;
use Eluceo\iCal\Property\Event\Attendees;
use Eluceo\iCal\Property\Event\Geo;
use Eluceo\iCal\Property\Event\Organizer;
use Eluceo\iCal\Property\Event\RecurrenceId;
use Eluceo\iCal\Property\Event\RecurrenceRule;
use Eluceo\iCal\Property\RawStringValue;
use Eluceo\iCal\PropertyBag;

/**
 * Implementation of the EVENT component.
 */
class Event extends Component
{
    const TIME_TRANSPARENCY_OPAQUE = 'OPAQUE';
    const TIME_TRANSPARENCY_TRANSPARENT = 'TRANSPARENT';

    const STATUS_TENTATIVE = 'TENTATIVE';
    const STATUS_CONFIRMED = 'CONFIRMED';
    const STATUS_CANCELLED = 'CANCELLED';

    const MS_BUSYSTATUS_FREE = 'FREE';
    const MS_BUSYSTATUS_TENTATIVE = 'TENTATIVE';
    const MS_BUSYSTATUS_BUSY = 'BUSY';
    const MS_BUSYSTATUS_OOF = 'OOF';

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
    protected $msBusyStatus = null;

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
     * @var Geo
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
     * @see https://tools.ietf.org/html/rfc5545#section-3.8.2.7
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
     * If set will be used as the timezone identifier.
     *
     * @var string
     */
    protected $timezoneString = '';

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
     * @var array
     */
    protected $recurrenceRules = [];

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
     * @var \DateTimeInterface[]
     */
    protected $exDates = [];

    /**
     * @var RecurrenceId
     */
    protected $recurrenceId;

    /**
     * @var Attachment[]
     */
    protected $attachments = [];

    public function __construct(string $uniqueId = null)
    {
        if (null == $uniqueId) {
            $uniqueId = uniqid();
        }

        $this->uniqueId = $uniqueId;
        $this->attendees = new Attendees();
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

        $propertyBag->add(new DateTimeProperty('DTSTART', $this->dtStart, $this->noTime, $this->useTimezone, $this->useUtc, $this->timezoneString));
        $propertyBag->set('SEQUENCE', $this->sequence);
        $propertyBag->set('TRANSP', $this->transparency);

        if ($this->status) {
            $propertyBag->set('STATUS', $this->status);
        }

        // An event can have a 'dtend' or 'duration', but not both.
        if ($this->dtEnd !== null) {
            $dtEnd = clone $this->dtEnd;
            if ($this->noTime === true) {
                $dtEnd = $dtEnd->add(new \DateInterval('P1D'));
            }
            $propertyBag->add(new DateTimeProperty('DTEND', $dtEnd, $this->noTime, $this->useTimezone, $this->useUtc, $this->timezoneString));
        } elseif ($this->duration !== null) {
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
                        new RawStringValue('geo:' . $this->locationGeo->getGeoLocationAsString(',')),
                        [
                            'VALUE' => 'URI',
                            'X-ADDRESS' => $this->location,
                            'X-APPLE-RADIUS' => 49,
                            'X-TITLE' => $this->locationTitle,
                        ]
                    )
                );
            }
        }

        if (null != $this->locationGeo) {
            $propertyBag->add($this->locationGeo);
        }

        if (null != $this->summary) {
            $propertyBag->set('SUMMARY', $this->summary);
        }

        if (null != $this->attendees) {
            $propertyBag->add($this->attendees);
        }

        $propertyBag->set('CLASS', $this->isPrivate ? 'PRIVATE' : 'PUBLIC');

        if (null != $this->description) {
            $propertyBag->set('DESCRIPTION', $this->description);
        }

        if (null != $this->descriptionHTML) {
            $propertyBag->add(
                new Property(
                    'X-ALT-DESC',
                    $this->descriptionHTML,
                    [
                        'FMTTYPE' => 'text/html',
                    ]
                )
            );
        }

        if (null != $this->recurrenceRule) {
            $propertyBag->set('RRULE', $this->recurrenceRule);
        }

        foreach ($this->recurrenceRules as $recurrenceRule) {
            $propertyBag->set('RRULE', $recurrenceRule);
        }

        if (null != $this->recurrenceId) {
            $this->recurrenceId->applyTimeSettings($this->noTime, $this->useTimezone, $this->useUtc, $this->timezoneString);
            $propertyBag->add($this->recurrenceId);
        }

        if (!empty($this->exDates)) {
            $propertyBag->add(new DateTimesProperty('EXDATE', $this->exDates, $this->noTime, $this->useTimezone, $this->useUtc, $this->timezoneString));
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

        if (null != $this->msBusyStatus) {
            $propertyBag->set('X-MICROSOFT-CDO-BUSYSTATUS', $this->msBusyStatus);
            $propertyBag->set('X-MICROSOFT-CDO-INTENDEDSTATUS', $this->msBusyStatus);
        }

        if (null != $this->categories) {
            $propertyBag->set('CATEGORIES', $this->categories);
        }

        $propertyBag->add(
            new DateTimeProperty('DTSTAMP', $this->dtStamp ?: new \DateTimeImmutable(), false, false, true)
        );

        if ($this->created) {
            $propertyBag->add(new DateTimeProperty('CREATED', $this->created, false, false, true));
        }

        if ($this->modified) {
            $propertyBag->add(new DateTimeProperty('LAST-MODIFIED', $this->modified, false, false, true));
        }

        foreach ($this->attachments as $attachment) {
            $propertyBag->add($attachment);
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

    public function getDtStart()
    {
        return $this->dtStart;
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
     * @param string     $location
     * @param string     $title
     * @param Geo|string $geo
     *
     * @return $this
     */
    public function setLocation($location, $title = '', $geo = null)
    {
        if (is_scalar($geo)) {
            $geo = Geo::fromString($geo);
        } elseif (!is_null($geo) && !$geo instanceof Geo) {
            $className = get_class($geo);
            throw new \InvalidArgumentException("The parameter 'geo' must be a string or an instance of " . Geo::class . " but an instance of {$className} was given.");
        }

        $this->location = $location;
        $this->locationTitle = $title;
        $this->locationGeo = $geo;

        return $this;
    }

    /**
     * @return $this
     */
    public function setGeoLocation(Geo $geoProperty)
    {
        $this->locationGeo = $geoProperty;

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
     * @param $msBusyStatus
     *
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function setMsBusyStatus($msBusyStatus)
    {
        $msBusyStatus = strtoupper($msBusyStatus);
        if ($msBusyStatus == self::MS_BUSYSTATUS_FREE
            || $msBusyStatus == self::MS_BUSYSTATUS_TENTATIVE
            || $msBusyStatus == self::MS_BUSYSTATUS_BUSY
            || $msBusyStatus == self::MS_BUSYSTATUS_OOF
        ) {
            $this->msBusyStatus = $msBusyStatus;
        } else {
            throw new \InvalidArgumentException('Invalid value for status');
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMsBusyStatus()
    {
        return $this->msBusyStatus;
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
     * @param $timezoneString
     *
     * @return $this
     */
    public function setTimezoneString($timezoneString)
    {
        $this->timezoneString = $timezoneString;

        return $this;
    }

    /**
     * @return bool
     */
    public function getTimezoneString()
    {
        return $this->timezoneString;
    }

    /**
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
    public function addAttendee($attendee, $params = [])
    {
        $this->attendees->add($attendee, $params);

        return $this;
    }

    public function getAttendees(): Attendees
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
     * @deprecated Deprecated since version 0.11.0, to be removed in 1.0. Use addRecurrenceRule instead.
     *
     * @return $this
     */
    public function setRecurrenceRule(RecurrenceRule $recurrenceRule)
    {
        @trigger_error('setRecurrenceRule() is deprecated since version 0.11.0 and will be removed in 1.0. Use addRecurrenceRule instead.', E_USER_DEPRECATED);

        $this->recurrenceRule = $recurrenceRule;

        return $this;
    }

    /**
     * @deprecated Deprecated since version 0.11.0, to be removed in 1.0. Use getRecurrenceRules instead.
     *
     * @return RecurrenceRule
     */
    public function getRecurrenceRule()
    {
        @trigger_error('getRecurrenceRule() is deprecated since version 0.11.0 and will be removed in 1.0. Use getRecurrenceRules instead.', E_USER_DEPRECATED);

        return $this->recurrenceRule;
    }

    /**
     * @return $this
     */
    public function addRecurrenceRule(RecurrenceRule $recurrenceRule)
    {
        $this->recurrenceRules[] = $recurrenceRule;

        return $this;
    }

    /**
     * @return array
     */
    public function getRecurrenceRules()
    {
        return $this->recurrenceRules;
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
     * @return \Eluceo\iCal\Component\Event
     */
    public function addExDate(\DateTimeInterface $dateTime)
    {
        $this->exDates[] = $dateTime;

        return $this;
    }

    /**
     * @return \DateTimeInterface[]
     */
    public function getExDates()
    {
        return $this->exDates;
    }

    /**
     * @param \DateTimeInterface[]
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
     * @return \Eluceo\iCal\Component\Event
     */
    public function setRecurrenceId(RecurrenceId $recurrenceId)
    {
        $this->recurrenceId = $recurrenceId;

        return $this;
    }

    /**
     * @param array $attachment
     *
     * @return $this
     */
    public function addAttachment(Attachment $attachment)
    {
        $this->attachments[] = $attachment;

        return $this;
    }

    /**
     * @return array
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    public function addUrlAttachment(string $url)
    {
        $this->addAttachment(new Attachment($url));
    }
}
