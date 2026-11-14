<?php

namespace Kanboard\Core\ICalendar;

use DateTimeImmutable;
use DateTimeInterface;

/**
 * Base class for iCalendar components that describe a task (VEVENT, VTODO)
 *
 * @package core
 * @author  Frederic Guillot
 */
abstract class Component
{
    private ?DateTimeImmutable $created = null;
    private ?DateTimeImmutable $modified = null;
    private string $summary = '';
    private string $description = '';
    private string $descriptionHtml = '';
    private string $url = '';

    public function __construct(private readonly string $uid)
    {
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

    /**
     * Get unfolded content lines
     *
     * Without METHOD, the DTSTAMP is the last revision of the component (RFC 5545 section 3.8.7.2),
     * the given stamp is only used when the modification date is unknown.
     *
     * @return list<string>
     */
    public function toLines(DateTimeInterface $stamp): array
    {
        $name = $this->name();

        $lines = [
            'BEGIN:'.$name,
            ContentLine::build('UID', ContentLine::text($this->uid)),
            ...$this->leadingLines(),
        ];

        if ($this->url !== '') {
            $lines[] = ContentLine::build('URL', ContentLine::uri($this->url));
        }

        if ($this->summary !== '') {
            $lines[] = ContentLine::build('SUMMARY', ContentLine::text($this->summary));
        }

        $lines[] = 'CLASS:PUBLIC';

        if ($this->description !== '') {
            $lines[] = ContentLine::build('DESCRIPTION', ContentLine::text($this->description));
        }

        if ($this->descriptionHtml !== '') {
            $lines[] = ContentLine::build('X-ALT-DESC', ContentLine::text($this->descriptionHtml), ['FMTTYPE' => 'text/html']);
        }

        array_push($lines, ...$this->trailingLines());

        $lines[] = ContentLine::build('DTSTAMP', ContentLine::utc($this->modified ?? $stamp));

        if ($this->created !== null) {
            $lines[] = ContentLine::build('CREATED', ContentLine::utc($this->created));
        }

        if ($this->modified !== null) {
            $lines[] = ContentLine::build('LAST-MODIFIED', ContentLine::utc($this->modified));
        }

        $lines[] = 'END:'.$name;

        return $lines;
    }

    /**
     * Get the component name, for example VEVENT
     */
    abstract protected function name(): string;

    /**
     * Get the component specific lines written after the UID
     *
     * @return list<string>
     */
    protected function leadingLines(): array
    {
        return [];
    }

    /**
     * Get the component specific lines written before the DTSTAMP
     *
     * @return list<string>
     */
    protected function trailingLines(): array
    {
        return [];
    }
}
