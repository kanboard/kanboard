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
use Eluceo\iCal\PropertyBag;

class Calendar extends Component
{
    /**
     * Methods for calendar components.
     *
     * According to RFP 5545: 3.7.2. Method
     *
     * @link http://tools.ietf.org/html/rfc5545#section-3.7.2
     *
     * And then according to RFC 2446: 3 APPLICATION PROTOCOL ELEMENTS
     * @link https://www.ietf.org/rfc/rfc2446.txt
     */
    const METHOD_PUBLISH        = 'PUBLISH';
    const METHOD_REQUEST        = 'REQUEST';
    const METHOD_REPLY          = 'REPLY';
    const METHOD_ADD            = 'ADD';
    const METHOD_CANCEL         = 'CANCEL';
    const METHOD_REFRESH        = 'REFRESH';
    const METHOD_COUNTER        = 'COUNTER';
    const METHOD_DECLINECOUNTER = 'DECLINECOUNTER';

    /**
     * This property defines the calendar scale used for the calendar information specified in the iCalendar object.
     *
     * According to RFC 5545: 3.7.1. Calendar Scale
     *
     * @link http://tools.ietf.org/html/rfc5545#section-3.7
     */
    const CALSCALE_GREGORIAN = 'GREGORIAN';

    /**
     * The Product Identifier.
     *
     * According to RFC 2445: 4.7.3 Product Identifier
     *
     * This property specifies the identifier for the product that created the Calendar object.
     *
     * @link http://www.ietf.org/rfc/rfc2445.txt
     *
     * @var string
     */
    protected $prodId      = null;
    protected $method      = null;
    protected $name        = null;
    protected $description = null;
    protected $timezone    = null;

    /**
     * This property defines the calendar scale used for the
     * calendar information specified in the iCalendar object.
     *
     * Also identifies the calendar type of a non-Gregorian recurring appointment.
     *
     * @var string
     *
     * @see http://tools.ietf.org/html/rfc5545#section-3.7
     * @see http://msdn.microsoft.com/en-us/library/ee237520(v=exchg.80).aspx
     */
    protected $calendarScale = null;

    /**
     * Specifies whether or not the iCalendar file only contains one appointment.
     *
     * @var bool
     *
     * @see http://msdn.microsoft.com/en-us/library/ee203486(v=exchg.80).aspx
     */
    protected $forceInspectOrOpen = false;

    /**
     * Specifies a globally unique identifier for the calendar.
     *
     * @var string
     *
     * @see http://msdn.microsoft.com/en-us/library/ee179588(v=exchg.80).aspx
     */
    protected $calId = null;

    /**
     * Specifies a suggested iCalendar file download frequency for clients and
     * servers with sync capabilities.
     *
     * @var string
     *
     * @see http://msdn.microsoft.com/en-us/library/ee178699(v=exchg.80).aspx
     */
    protected $publishedTTL = 'P1W';

    /**
     * Specifies a color for the calendar in calendar for Apple/Outlook.
     *
     * @var string
     *
     * @see http://msdn.microsoft.com/en-us/library/ee179588(v=exchg.80).aspx
     */
    protected $calendarColor = null;

    public function __construct($prodId)
    {
        if (empty($prodId)) {
            throw new \UnexpectedValueException('PRODID cannot be empty');
        }

        $this->prodId = $prodId;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'VCALENDAR';
    }

    /**
     * @param $method
     *
     * @return $this
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @param $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
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
     * @param $timezone
     *
     * @return $this
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * @param $calendarColor
     *
     * @return $this
     */
    public function setCalendarColor($calendarColor)
    {
        $this->calendarColor = $calendarColor;

        return $this;
    }

    /**
     * @param $calendarScale
     *
     * @return $this
     */
    public function setCalendarScale($calendarScale)
    {
        $this->calendarScale = $calendarScale;

        return $this;
    }

    /**
     * @param bool $forceInspectOrOpen
     *
     * @return $this
     */
    public function setForceInspectOrOpen($forceInspectOrOpen)
    {
        $this->forceInspectOrOpen = $forceInspectOrOpen;

        return $this;
    }

    /**
     * @param string $calId
     *
     * @return $this
     */
    public function setCalId($calId)
    {
        $this->calId = $calId;

        return $this;
    }

    /**
     * @param string $ttl
     *
     * @return $this
     */
    public function setPublishedTTL($ttl)
    {
        $this->publishedTTL = $ttl;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function buildPropertyBag()
    {
        $propertyBag = new PropertyBag();
        $propertyBag->set('VERSION', '2.0');
        $propertyBag->set('PRODID', $this->prodId);

        if ($this->method) {
            $propertyBag->set('METHOD', $this->method);
        }

        if ($this->calendarColor) {
            $propertyBag->set('X-APPLE-CALENDAR-COLOR', $this->calendarColor);
            $propertyBag->set('X-OUTLOOK-COLOR', $this->calendarColor);
            $propertyBag->set('X-FUNAMBOL-COLOR', $this->calendarColor);
        }

        if ($this->calendarScale) {
            $propertyBag->set('CALSCALE', $this->calendarScale);
            $propertyBag->set('X-MICROSOFT-CALSCALE', $this->calendarScale);
        }

        if ($this->name) {
            $propertyBag->set('X-WR-CALNAME', $this->name);
        }

        if ($this->description) {
            $propertyBag->set('X-WR-CALDESC', $this->description);
        }

        if ($this->timezone) {
            if ($this->timezone instanceof Timezone) {
                $propertyBag->set('X-WR-TIMEZONE', $this->timezone->getZoneIdentifier());
                $this->addComponent($this->timezone);
            } else {
                $propertyBag->set('X-WR-TIMEZONE', $this->timezone);
                $this->addComponent(new Timezone($this->timezone));
            }
        }

        if ($this->forceInspectOrOpen) {
            $propertyBag->set('X-MS-OLK-FORCEINSPECTOROPEN', $this->forceInspectOrOpen);
        }

        if ($this->calId) {
            $propertyBag->set('X-WR-RELCALID', $this->calId);
        }

        if ($this->publishedTTL) {
            $propertyBag->set('X-PUBLISHED-TTL', $this->publishedTTL);
        }

        return $propertyBag;
    }

    /**
     * Adds an Event to the Calendar.
     *
     * Wrapper for addComponent()
     *
     * @see        Eluceo\iCal::addComponent
     * @deprecated Please, use public method addComponent() from abstract Component class
     *
     * @param Event $event
     */
    public function addEvent(Event $event)
    {
        $this->addComponent($event);
    }

    /**
     * @return null|string
     */
    public function getProdId()
    {
        return $this->prodId;
    }

    public function getMethod()
    {
        return $this->method;
    }
}
