<?php

namespace PhpOffice\Math\Element;

class Fraction extends AbstractElement
{
    /**
     * @var AbstractElement
     */
    protected $denominator;

    /**
     * @var AbstractElement
     */
    protected $numerator;

    public function __construct(AbstractElement $numerator, AbstractElement $denominator)
    {
        $this->setNumerator($numerator);
        $this->setDenominator($denominator);
    }

    public function getDenominator(): AbstractElement
    {
        return $this->denominator;
    }

    public function getNumerator(): AbstractElement
    {
        return $this->numerator;
    }

    public function setDenominator(AbstractElement $element): self
    {
        $this->denominator = $element;

        return $this;
    }

    public function setNumerator(AbstractElement $element): self
    {
        $this->numerator = $element;

        return $this;
    }
}
