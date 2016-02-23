<?php

/*
 * This file is part of the eluceo/iCal package.
 *
 * (c) Markus Poerschke <markus@eluceo.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eluceo\iCal\Component;

use Eluceo\iCal\Component;
use Eluceo\iCal\PropertyBag;

/**
 * Implementation of the TIMEZONE component.
 */
class Timezone extends Component
{
    /**
     * @var string
     */
    protected $timezone;

    public function __construct($timezone)
    {
        $this->timezone = $timezone;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'VTIMEZONE';
    }

    /**
     * {@inheritdoc}
     */
    public function buildPropertyBag()
    {
        $propertyBag = new PropertyBag();

        $propertyBag->set('TZID', $this->timezone);
        $propertyBag->set('X-LIC-LOCATION', $this->timezone);

        return $propertyBag;
    }

    public function getZoneIdentifier()
    {
        return $this->timezone;
    }
}
