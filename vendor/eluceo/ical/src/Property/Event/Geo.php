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
 * GEO property.
 *
 * @see https://tools.ietf.org/html/rfc5545#section-3.8.1.6
 */
class Geo extends Property
{
    /**
     * @var float
     */
    private $latitude;

    /**
     * @var float
     */
    private $longitude;

    public function __construct(float $latitude, float $longitude)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;

        if ($this->latitude < -90 || $this->latitude > 90) {
            throw new \InvalidArgumentException("The geographical latitude must be a value between -90 and 90 degrees. '{$this->latitude}' was given.");
        }

        if ($this->longitude < -180 || $this->longitude > 180) {
            throw new \InvalidArgumentException("The geographical longitude must be a value between -180 and 180 degrees. '{$this->longitude}' was given.");
        }

        parent::__construct('GEO', new Property\RawStringValue($this->getGeoLocationAsString()));
    }

    /**
     * @deprecated This method is used to allow backwards compatibility for Event::setLocation
     *
     * @return Geo
     */
    public static function fromString(string $geoLocationString): self
    {
        $geoLocationString = str_replace(',', ';', $geoLocationString);
        $geoLocationString = str_replace('GEO:', '', $geoLocationString);
        $parts = explode(';', $geoLocationString);

        return new static((float) $parts[0], (float) $parts[1]);
    }

    /**
     * Returns the coordinates as a string.
     *
     * @example 37.386013;-122.082932
     */
    public function getGeoLocationAsString(string $separator = ';'): string
    {
        return number_format($this->latitude, 6) . $separator . number_format($this->longitude, 6);
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }
}
