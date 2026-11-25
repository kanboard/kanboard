<?php

namespace PhpOffice\Math\Element;

class Numeric extends AbstractElement
{
    /**
     * @var float
     */
    protected $value;

    public function __construct(float $value)
    {
        $this->value = $value;
    }

    public function getValue(): float
    {
        return $this->value;
    }
}
