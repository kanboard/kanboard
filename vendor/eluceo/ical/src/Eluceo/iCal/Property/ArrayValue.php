<?php

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
