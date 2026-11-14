<?php

namespace KanboardTests\units\Core\ICalendar;

use DateTimeImmutable;
use DateTimeZone;
use Kanboard\Core\ICalendar\Calendar;
use Kanboard\Core\ICalendar\Event;
use Kanboard\Core\ICalendar\Todo;
use LogicException;
use PHPUnit\Framework\TestCase;

class CalendarTest extends TestCase
{
    private DateTimeImmutable $stamp;
    private DateTimeZone $timezone;

    protected function setUp(): void
    {
        $this->stamp = new DateTimeImmutable('2026-11-13 18:50:55', new DateTimeZone('UTC'));
        $this->timezone = new DateTimeZone('Europe/Paris');
    }

    public function testEmptyCalendar()
    {
        $calendar = new Calendar('Kanboard');

        $this->assertSame("BEGIN:VCALENDAR\r\nVERSION:2.0\r\nPRODID:Kanboard\r\nEND:VCALENDAR\r\n", $calendar->render($this->stamp));
    }

    public function testRender()
    {
        $created = new DateTimeImmutable('2026-11-01 10:00:00', $this->timezone);
        $allDay = new DateTimeImmutable('2026-11-20 00:00:00', $this->timezone);

        $calendar = (new Calendar('Kanboard', 'My, project', 'My project', 'PT1H'))
            ->addEvent(
                (new Event('task-#1-date_started-date_due'))
                    ->setStart(new DateTimeImmutable('2026-11-14 23:13:20', $this->timezone))
                    ->setEnd(new DateTimeImmutable('2026-11-15 08:00:00', $this->timezone))
                    ->setCreated($created)
                    ->setModified($created)
                    ->setSummary('#1 Fix "quotes"; commas, too')
                    ->setDescription("line1\r\nline2")
                    ->setDescriptionHtml('<p>line1<br>line2</p>')
                    ->setUrl('http://localhost/?controller=TaskViewController&action=show&task_id=1')
                    ->addAttendee('john@example.org', 'Doe, John')
                    ->setOrganizer('admin@kanboard.local', 'admin')
            )
            ->addEvent(
                (new Event('task-#2-date_due'))
                    ->setStart($allDay)
                    ->setEnd($allDay)
                    ->setAllDay()
                    ->setCreated($created)
                    ->setModified($created)
                    ->setSummary('#2 All day')
            );

        $expected = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:Kanboard',
            'X-WR-CALNAME:My\\, project',
            'X-WR-CALDESC:My project',
            'X-PUBLISHED-TTL:PT1H',
            'BEGIN:VEVENT',
            'UID:task-#1-date_started-date_due',
            'DTSTART;TZID=Europe/Paris:20261114T231320',
            'SEQUENCE:0',
            'TRANSP:OPAQUE',
            'DTEND;TZID=Europe/Paris:20261115T080000',
            'URL:http://localhost/?controller=TaskViewController&action=show&task_id=1',
            'SUMMARY:#1 Fix "quotes"\\; commas\\, too',
            'ATTENDEE;CN="Doe, John":MAILTO:john@example.org',
            'CLASS:PUBLIC',
            'DESCRIPTION:line1\\nline2',
            'X-ALT-DESC;FMTTYPE=text/html:<p>line1<br>line2</p>',
            'ORGANIZER;CN=admin:MAILTO:admin@kanboard.local',
            'DTSTAMP:20261113T185055Z',
            'CREATED:20261101T090000Z',
            'LAST-MODIFIED:20261101T090000Z',
            'END:VEVENT',
            'BEGIN:VEVENT',
            'UID:task-#2-date_due',
            'DTSTART;VALUE=DATE:20261120',
            'SEQUENCE:0',
            'TRANSP:OPAQUE',
            'DTEND;VALUE=DATE:20261121',
            'SUMMARY:#2 All day',
            'CLASS:PUBLIC',
            'X-MICROSOFT-CDO-ALLDAYEVENT:TRUE',
            'DTSTAMP:20261113T185055Z',
            'CREATED:20261101T090000Z',
            'LAST-MODIFIED:20261101T090000Z',
            'END:VEVENT',
            'END:VCALENDAR',
        ];

        $this->assertSame(implode("\r\n", $expected)."\r\n", $calendar->render($this->stamp));
    }

    public function testRenderTodo()
    {
        $created = new DateTimeImmutable('2026-11-01 10:00:00', $this->timezone);

        $calendar = (new Calendar('Kanboard'))
            ->addTodo(
                (new Todo('task-#3-todo'))
                    ->setCreated($created)
                    ->setModified($created)
                    ->setSummary('#3 No dates; yet')
                    ->setDescription("line1\nline2")
                    ->setDescriptionHtml('<p>line1<br>line2</p>')
                    ->setUrl('http://localhost/?controller=TaskViewController&action=show&task_id=3')
                    ->addAttendee('john@example.org', 'Doe, John')
                    ->setOrganizer('admin@kanboard.local', 'admin')
            );

        $expected = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:Kanboard',
            'BEGIN:VTODO',
            'UID:task-#3-todo',
            'STATUS:NEEDS-ACTION',
            'URL:http://localhost/?controller=TaskViewController&action=show&task_id=3',
            'SUMMARY:#3 No dates\\; yet',
            'ATTENDEE;CN="Doe, John":MAILTO:john@example.org',
            'CLASS:PUBLIC',
            'DESCRIPTION:line1\\nline2',
            'X-ALT-DESC;FMTTYPE=text/html:<p>line1<br>line2</p>',
            'ORGANIZER;CN=admin:MAILTO:admin@kanboard.local',
            'DTSTAMP:20261113T185055Z',
            'CREATED:20261101T090000Z',
            'LAST-MODIFIED:20261101T090000Z',
            'END:VTODO',
            'END:VCALENDAR',
        ];

        $this->assertSame(implode("\r\n", $expected)."\r\n", $calendar->render($this->stamp));
    }

    public function testTodoWithoutOptionalProperties()
    {
        $output = (new Calendar('Kanboard'))->addTodo(new Todo('uid'))->render($this->stamp);

        $this->assertStringContainsString(
            "BEGIN:VTODO\r\nUID:uid\r\nSTATUS:NEEDS-ACTION\r\nCLASS:PUBLIC\r\nDTSTAMP:20261113T185055Z\r\nEND:VTODO\r\n",
            $output
        );
    }

    public function testEventsAndTodosAreRenderedInInsertionOrder()
    {
        $output = (new Calendar('Kanboard'))
            ->addTodo(new Todo('first'))
            ->addEvent((new Event('second'))->setStart($this->stamp))
            ->render($this->stamp);

        $this->assertLessThan(strpos($output, 'BEGIN:VEVENT'), strpos($output, 'BEGIN:VTODO'));
        $this->assertSame(1, substr_count($output, 'BEGIN:VEVENT'));
        $this->assertSame(1, substr_count($output, 'BEGIN:VTODO'));
    }

    public function testLongLinesAreFolded()
    {
        $summary = str_repeat('é', 100);
        $calendar = (new Calendar('Kanboard'))
            ->addEvent((new Event('uid'))->setStart($this->stamp)->setSummary($summary));

        $output = $calendar->render($this->stamp);

        foreach (explode("\r\n", rtrim($output, "\r\n")) as $line) {
            $this->assertLessThanOrEqual(75, strlen($line));
        }

        $this->assertStringContainsString('SUMMARY:'.$summary."\r\n", str_replace("\r\n ", '', $output));
    }

    public function testEventWithoutOptionalProperties()
    {
        $calendar = (new Calendar('Kanboard'))
            ->addEvent((new Event('uid'))->setStart($this->stamp)->addAttendee('a@b.c'));

        $output = $calendar->render($this->stamp);

        $this->assertStringContainsString("\r\nATTENDEE:MAILTO:a@b.c\r\n", $output);
        $this->assertStringNotContainsString('DTEND', $output);
        $this->assertStringNotContainsString('ORGANIZER', $output);
        $this->assertStringNotContainsString('DESCRIPTION', $output);
        $this->assertStringNotContainsString('CREATED', $output);
    }

    public function testEventRequiresStartDate()
    {
        $this->expectException(LogicException::class);
        (new Calendar('Kanboard'))->addEvent(new Event('uid'))->render($this->stamp);
    }

    public function testEventDoesNotModifyGivenDates()
    {
        $date = new \DateTime('2026-11-20 00:00:00', $this->timezone);
        $event = (new Event('uid'))->setStart($date)->setEnd($date)->setAllDay();

        (new Calendar('Kanboard'))->addEvent($event)->render($this->stamp);

        $this->assertSame('2026-11-20', $date->format('Y-m-d'));
    }
}
