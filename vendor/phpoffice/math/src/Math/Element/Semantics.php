<?php

declare(strict_types=1);

namespace PhpOffice\Math\Element;

class Semantics extends AbstractGroupElement
{
    /**
     * @var array<string, string>
     */
    protected $annotations = [];

    public function addAnnotation(string $encoding, string $annotation): self
    {
        $this->annotations[$encoding] = $annotation;

        return $this;
    }

    public function getAnnotation(string $encoding): ?string
    {
        return $this->annotations[$encoding] ?? null;
    }

    /**
     * @return array<string, string>
     */
    public function getAnnotations(): array
    {
        return $this->annotations;
    }
}
