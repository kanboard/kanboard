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

/**
 * Implementation of the VALARM component.
 */
class Alarm extends Component
{
    /**
     * Alarm ACTION property.
     *
     * According to RFC 5545: 3.8.6.1. Action
     *
     * @see http://tools.ietf.org/html/rfc5545#section-3.8.6.1
     */
    const ACTION_AUDIO = 'AUDIO';
    const ACTION_DISPLAY = 'DISPLAY';
    const ACTION_EMAIL = 'EMAIL';

    protected $action;
    protected $repeat;
    protected $duration;
    protected $description;
    protected $attendee;
    protected $trigger;

    public function getType()
    {
        return 'VALARM';
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getRepeat()
    {
        return $this->repeat;
    }

    public function getDuration()
    {
        return $this->duration;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getAttendee()
    {
        return $this->attendee;
    }

    public function getTrigger()
    {
        return $this->trigger;
    }

    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    public function setRepeat($repeat)
    {
        $this->repeat = $repeat;

        return $this;
    }

    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function setAttendee($attendee)
    {
        $this->attendee = $attendee;

        return $this;
    }

    public function setTrigger($trigger)
    {
        $this->trigger = $trigger;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function buildPropertyBag()
    {
        $propertyBag = new PropertyBag();

        if (null != $this->trigger) {
            $propertyBag->set('TRIGGER', $this->trigger);
        }

        if (null != $this->action) {
            $propertyBag->set('ACTION', $this->action);
        }

        if (null != $this->repeat) {
            $propertyBag->set('REPEAT', $this->repeat);
        }

        if (null != $this->duration) {
            $propertyBag->set('DURATION', $this->duration);
        }

        if (null != $this->description) {
            $propertyBag->set('DESCRIPTION', $this->description);
        }

        if (null != $this->attendee) {
            $propertyBag->set('ATTENDEE', $this->attendee);
        }

        return $propertyBag;
    }
}
