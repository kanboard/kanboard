<?php

namespace Kanboard\Core\ICalendar;

use DateTimeImmutable;
use DateTimeInterface;
use LogicException;

/**
 * iCalendar VEVENT component
 *
 * Timed events are written in local time with the TZID of their start date,
 * all-day events are written as dates with an exclusive end date.
 *
 * @package core
 * @author  Frederic Guillot
 */
final class Event extends Component
{
    private ?DateTimeImmutable $start = null;
    private ?DateTimeImmutable $end = null;
    private bool $allDay = false;

    public function setStart(DateTimeInterface $start): static
    {
        $this->start = DateTimeImmutable::createFromInterface($start);
        return $this;
    }

    public function setEnd(DateTimeInterface $end): static
    {
        $this->end = DateTimeImmutable::createFromInterface($end);
        return $this;
    }

    public function setAllDay(bool $allDay = true): static
    {
        $this->allDay = $allDay;
        return $this;
    }

    protected function name(): string
    {
        return 'VEVENT';
    }

    protected function leadingLines(): array
    {
        if ($this->start === null) {
            throw new LogicException('Event start date is required');
        }

        $lines = [
            $this->dateLine('DTSTART', $this->start),
            'SEQUENCE:0',
            'TRANSP:OPAQUE',
        ];

        if ($this->end !== null) {
            $lines[] = $this->dateLine('DTEND', $this->allDay ? $this->end->modify('+1 day') : $this->end);
        }

        return $lines;
    }

    protected function trailingLines(): array
    {
        return $this->allDay ? ['X-MICROSOFT-CDO-ALLDAYEVENT:TRUE'] : [];
    }

    private function dateLine(string $name, DateTimeImmutable $date): string
    {
        if ($this->allDay) {
            return ContentLine::build($name, ContentLine::date($date), ['VALUE' => 'DATE']);
        }

        return ContentLine::build($name, ContentLine::local($date), ['TZID' => $date->getTimezone()->getName()]);
    }
}
