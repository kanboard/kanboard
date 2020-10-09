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

use Eluceo\iCal\Property;

/**
 * Class Organizer.
 */
class Organizer extends Property
{
    /**
     * @param string $value
     * @param array  $params
     */
    public function __construct($value, $params = [])
    {
        parent::__construct('ORGANIZER', $value, $params);
    }
}
