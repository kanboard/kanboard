<?php

namespace Kanboard\Core\ICalendar;

use DateTimeImmutable;
use DateTimeInterface;
use LogicException;

/**
 * iCalendar VEVENT component
 *
 * Timed events are written in UTC, and all-day events are written as dates
 * with an exclusive end date. The end date is omitted when the event does not
 * end after its start.
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

        $end = $this->endDate();

        if ($end !== null) {
            $lines[] = $this->dateLine('DTEND', $end);
        }

        return $lines;
    }

    protected function trailingLines(): array
    {
        return $this->allDay ? ['X-MICROSOFT-CDO-ALLDAYEVENT:TRUE'] : [];
    }

    /**
     * Get the end date to write, which must be later than the start date (RFC 5545 section 3.8.2.2)
     *
     * Without end date, an all-day event lasts one day and a timed event ends at its start.
     */
    private function endDate(): ?DateTimeImmutable
    {
        if ($this->end === null) {
            return null;
        }

        if ($this->allDay) {
            return ContentLine::date($this->end) >= ContentLine::date($this->start) ? $this->end->modify('+1 day') : null;
        }

        return $this->end > $this->start ? $this->end : null;
    }

    private function dateLine(string $name, DateTimeImmutable $date): string
    {
        if ($this->allDay) {
            return ContentLine::build($name, ContentLine::date($date), ['VALUE' => 'DATE']);
        }

        return ContentLine::build($name, ContentLine::utc($date));
    }
}
