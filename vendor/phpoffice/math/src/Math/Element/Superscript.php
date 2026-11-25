<?php

namespace PhpOffice\Math\Element;

class Superscript extends AbstractElement
{
    /**
     * @var AbstractElement
     */
    protected $base;

    /**
     * @var AbstractElement
     */
    protected $superscript;

    public function __construct(AbstractElement $base, AbstractElement $superscript)
    {
        $this->setBase($base);
        $this->setSuperscript($superscript);
    }

    public function getBase(): AbstractElement
    {
        return $this->base;
    }

    public function getSuperscript(): AbstractElement
    {
        return $this->superscript;
    }

    public function setBase(AbstractElement $element): self
    {
        $this->base = $element;

        return $this;
    }

    public function setSuperscript(AbstractElement $element): self
    {
        $this->superscript = $element;

        return $this;
    }
}
