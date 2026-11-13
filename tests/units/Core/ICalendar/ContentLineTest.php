<?php

namespace KanboardTests\units\Core\ICalendar;

use DateTimeImmutable;
use DateTimeZone;
use Kanboard\Core\ICalendar\ContentLine;
use PHPUnit\Framework\TestCase;

class ContentLineTest extends TestCase
{
    public function testTextEscapesSpecialCharacters()
    {
        $this->assertSame('a\\\\b\\;c\\,d\\ne', ContentLine::text("a\\b;c,d\ne"));
        $this->assertSame('say "hi": ok', ContentLine::text('say "hi": ok'));
    }

    public function testTextNormalizesLineEndings()
    {
        $this->assertSame('a\\nb\\nc', ContentLine::text("a\r\nb\rc"));
    }

    public function testTextStripsControlCharactersExceptTab()
    {
        $this->assertSame("a\tb", ContentLine::text("a\x00\tb\x1B\x7F"));
    }

    public function testUriIsNotEscaped()
    {
        $this->assertSame('http://x/?a=1;b=2,c', ContentLine::uri('http://x/?a=1;b=2,c'));
        $this->assertSame('MAILTO:a@b.cBEGIN:VEVENT', ContentLine::uri("MAILTO:a@b.c\r\nBEGIN:VEVENT"));
    }

    public function testParamEncoding()
    {
        $this->assertSame('admin', ContentLine::param('admin'));
        $this->assertSame('"Doe, John"', ContentLine::param('Doe, John'));
        $this->assertSame('"a;b"', ContentLine::param('a;b'));
        $this->assertSame('"a:b"', ContentLine::param('a:b'));
        $this->assertSame("^'J^' ^^ a^nb", ContentLine::param("\"J\" ^ a\r\nb"));
    }

    public function testBuild()
    {
        $this->assertSame('SUMMARY:hello', ContentLine::build('SUMMARY', 'hello'));
        $this->assertSame(
            'ATTENDEE;CN="Doe, J";ROLE=CHAIR:MAILTO:a@b.c',
            ContentLine::build('ATTENDEE', 'MAILTO:a@b.c', ['CN' => 'Doe, J', 'ROLE' => 'CHAIR'])
        );
    }

    public function testShortLineIsNotFolded()
    {
        $line = str_repeat('a', 75);
        $this->assertSame([$line], ContentLine::fold($line));
    }

    public function testFoldAsciiLine()
    {
        $lines = ContentLine::fold(str_repeat('a', 200));

        $this->assertSame([str_repeat('a', 75), ' '.str_repeat('a', 74), ' '.str_repeat('a', 51)], $lines);
    }

    public function testFoldDoesNotSplitMultibyteCharacters()
    {
        $line = 'SUMMARY:'.str_repeat('é', 20).str_repeat('🎉', 30).str_repeat('日本', 20);
        $lines = ContentLine::fold($line);

        $this->assertGreaterThan(1, count($lines));

        foreach ($lines as $index => $folded) {
            $this->assertLessThanOrEqual(75, strlen($folded));
            $this->assertTrue(mb_check_encoding($folded, 'UTF-8'));

            if ($index > 0) {
                $this->assertSame(' ', $folded[0]);
            }
        }

        $this->assertSame($line, implode('', array_map(fn ($l, $i) => $i === 0 ? $l : substr($l, 1), $lines, array_keys($lines))));
    }

    public function testDateFormats()
    {
        $date = new DateTimeImmutable('2026-03-29 01:30:00', new DateTimeZone('Europe/Paris'));

        $this->assertSame('20260329T003000Z', ContentLine::utc($date));
        $this->assertSame('20260329T013000', ContentLine::local($date));
        $this->assertSame('20260329', ContentLine::date($date));
    }
}
