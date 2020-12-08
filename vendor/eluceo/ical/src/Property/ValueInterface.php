<?php

/*
 * This file is part of the eluceo/iCal package.
 *
 * (c) Markus Poerschke <markus@eluceo.de>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Eluceo\iCal\Property;

interface ValueInterface
{
    /**
     * Return the value of the Property as an escaped string.
     *
     * Escape values as per RFC 5545.
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.3.11
     */
    public function getEscapedValue(): string;
}
