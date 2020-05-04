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

use Eluceo\iCal\Util\PropertyValueUtil;

class ArrayValue implements ValueInterface
{
    /**
     * The value.
     *
     * @var array
     */
    protected $values;

    public function __construct(array $values)
    {
        $this->values = $values;
    }

    public function setValues(array $values)
    {
        $this->values = $values;

        return $this;
    }

    public function getEscapedValue()
    {
        return implode(',', array_map(function ($value) {
            return PropertyValueUtil::escapeValue((string) $value);
        }, $this->values));
    }
}
