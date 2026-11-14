<?php

namespace Kanboard\Core\ICalendar;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;

/**
 * iCalendar VCALENDAR object writer (RFC 5545)
 *
 * @package core
 * @author  Frederic Guillot
 */
final class Calendar
{
    /** @var list<Component> */
    private array $components = [];

    public function __construct(
        private readonly string $productId,
        private readonly string $name = '',
        private readonly string $description = '',
        private readonly string $publishedTtl = '',
    ) {
    }

    public function addEvent(Event $event): static
    {
        $this->components[] = $event;
        return $this;
    }

    public function addTodo(Todo $todo): static
    {
        $this->components[] = $todo;
        return $this;
    }

    public function render(?DateTimeInterface $stamp = null): string
    {
        $stamp ??= new DateTimeImmutable('now', new DateTimeZone('UTC'));

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            ContentLine::build('PRODID', ContentLine::text($this->productId)),
        ];

        if ($this->name !== '') {
            $lines[] = ContentLine::build('X-WR-CALNAME', ContentLine::text($this->name));
        }

        if ($this->description !== '') {
            $lines[] = ContentLine::build('X-WR-CALDESC', ContentLine::text($this->description));
        }

        if ($this->publishedTtl !== '') {
            $lines[] = ContentLine::build('X-PUBLISHED-TTL', ContentLine::text($this->publishedTtl));
        }

        foreach ($this->components as $component) {
            array_push($lines, ...$component->toLines($stamp));
        }

        $lines[] = 'END:VCALENDAR';

        $output = '';

        foreach ($lines as $line) {
            foreach (ContentLine::fold($line) as $folded) {
                $output .= $folded."\r\n";
            }
        }

        return $output;
    }
}
