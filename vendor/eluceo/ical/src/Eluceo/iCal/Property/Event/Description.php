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

use Eluceo\iCal\Property\ValueInterface;
use Eluceo\iCal\Util\PropertyValueUtil;

/**
 * Class Description
 * Alows new line charectars to be in the description.
 */
class Description implements ValueInterface
{
    /**
     * The value.
     *
     * @var string
     */
    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Return the value of the Property as an escaped string.
     *
     * Escape values as per RFC 2445. See http://www.kanzaki.com/docs/ical/text.html
     *
     * @return string
     */
    public function getEscapedValue()
    {
        return PropertyValueUtil::escapeValue((string) $this->value);
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}
