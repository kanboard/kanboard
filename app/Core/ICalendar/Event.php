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
final class Event
{
    private ?DateTimeImmutable $start = null;
    private ?DateTimeImmutable $end = null;
    private bool $allDay = false;
    private ?DateTimeImmutable $created = null;
    private ?DateTimeImmutable $modified = null;
    private string $summary = '';
    private string $description = '';
    private string $descriptionHtml = '';
    private string $url = '';

    /** @var list<array{email: string, name: string}> */
    private array $attendees = [];

    /** @var array{email: string, name: string}|null */
    private ?array $organizer = null;

    public function __construct(private readonly string $uid)
    {
    }

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

    public function setCreated(DateTimeInterface $created): static
    {
        $this->created = DateTimeImmutable::createFromInterface($created);
        return $this;
    }

    public function setModified(DateTimeInterface $modified): static
    {
        $this->modified = DateTimeImmutable::createFromInterface($modified);
        return $this;
    }

    public function setSummary(string $summary): static
    {
        $this->summary = $summary;
        return $this;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function setDescriptionHtml(string $descriptionHtml): static
    {
        $this->descriptionHtml = $descriptionHtml;
        return $this;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;
        return $this;
    }

    public function addAttendee(string $email, string $name = ''): static
    {
        $this->attendees[] = ['email' => $email, 'name' => $name];
        return $this;
    }

    public function setOrganizer(string $email, string $name = ''): static
    {
        $this->organizer = ['email' => $email, 'name' => $name];
        return $this;
    }

    /**
     * Get unfolded content lines
     *
     * @return list<string>
     */
    public function toLines(DateTimeInterface $stamp): array
    {
        if ($this->start === null) {
            throw new LogicException('Event start date is required');
        }

        $lines = [
            'BEGIN:VEVENT',
            ContentLine::build('UID', ContentLine::text($this->uid)),
            $this->dateLine('DTSTART', $this->start),
            'SEQUENCE:0',
            'TRANSP:OPAQUE',
        ];

        if ($this->end !== null) {
            $lines[] = $this->dateLine('DTEND', $this->allDay ? $this->end->modify('+1 day') : $this->end);
        }

        if ($this->url !== '') {
            $lines[] = ContentLine::build('URL', ContentLine::uri($this->url));
        }

        if ($this->summary !== '') {
            $lines[] = ContentLine::build('SUMMARY', ContentLine::text($this->summary));
        }

        foreach ($this->attendees as $attendee) {
            $lines[] = $this->addressLine('ATTENDEE', $attendee);
        }

        $lines[] = 'CLASS:PUBLIC';

        if ($this->description !== '') {
            $lines[] = ContentLine::build('DESCRIPTION', ContentLine::text($this->description));
        }

        if ($this->descriptionHtml !== '') {
            $lines[] = ContentLine::build('X-ALT-DESC', ContentLine::text($this->descriptionHtml), ['FMTTYPE' => 'text/html']);
        }

        if ($this->organizer !== null) {
            $lines[] = $this->addressLine('ORGANIZER', $this->organizer);
        }

        if ($this->allDay) {
            $lines[] = 'X-MICROSOFT-CDO-ALLDAYEVENT:TRUE';
        }

        $lines[] = ContentLine::build('DTSTAMP', ContentLine::utc($stamp));

        if ($this->created !== null) {
            $lines[] = ContentLine::build('CREATED', ContentLine::utc($this->created));
        }

        if ($this->modified !== null) {
            $lines[] = ContentLine::build('LAST-MODIFIED', ContentLine::utc($this->modified));
        }

        $lines[] = 'END:VEVENT';

        return $lines;
    }

    private function dateLine(string $name, DateTimeImmutable $date): string
    {
        if ($this->allDay) {
            return ContentLine::build($name, ContentLine::date($date), ['VALUE' => 'DATE']);
        }

        return ContentLine::build($name, ContentLine::local($date), ['TZID' => $date->getTimezone()->getName()]);
    }

    /**
     * @param array{email: string, name: string} $address
     */
    private function addressLine(string $name, array $address): string
    {
        $params = $address['name'] !== '' ? ['CN' => $address['name']] : [];
        return ContentLine::build($name, ContentLine::uri('MAILTO:'.$address['email']), $params);
    }
}
