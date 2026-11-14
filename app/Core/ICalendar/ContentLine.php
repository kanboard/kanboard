<?php

namespace Kanboard\Core\ICalendar;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;

/**
 * iCalendar content line encoding (RFC 5545 section 3.1 and RFC 6868)
 *
 * @package core
 * @author  Frederic Guillot
 */
final class ContentLine
{
    private const MAX_OCTETS = 75;
    private const CONTROL_CHARS = '/[\x00-\x08\x0A-\x1F\x7F]/';

    /**
     * Build an unfolded content line, the value must already be encoded
     *
     * @param array<string, string> $params
     */
    public static function build(string $name, string $value, array $params = []): string
    {
        $line = $name;

        foreach ($params as $param => $paramValue) {
            $line .= ';'.$param.'='.self::param($paramValue);
        }

        return $line.':'.$value;
    }

    /**
     * Encode a TEXT value (RFC 5545 section 3.3.11)
     */
    public static function text(string $value): string
    {
        $value = str_replace(["\r\n", "\r"], "\n", $value);
        $value = strtr($value, ['\\' => '\\\\', ';' => '\\;', ',' => '\\,', "\n" => '\\n']);

        return (string) preg_replace(self::CONTROL_CHARS, '', $value);
    }

    /**
     * Encode a URI value, which is not escaped
     */
    public static function uri(string $value): string
    {
        return (string) preg_replace('/[\x00-\x1F\x7F]/', '', $value);
    }

    /**
     * Encode a parameter value (RFC 6868)
     */
    public static function param(string $value): string
    {
        $value = str_replace(["\r\n", "\r"], "\n", $value);
        $value = strtr($value, ['^' => '^^', "\n" => '^n', '"' => "^'"]);
        $value = (string) preg_replace(self::CONTROL_CHARS, '', $value);

        return strpbrk($value, ';:,') === false ? $value : '"'.$value.'"';
    }

    /**
     * Split a content line into lines of 75 octets at most, without breaking UTF-8 sequences
     *
     * @return list<string>
     */
    public static function fold(string $line): array
    {
        $lines = [];

        while (strlen($line) > self::MAX_OCTETS) {
            $chunk = mb_strcut($line, 0, self::MAX_OCTETS, 'UTF-8');
            $lines[] = $chunk;
            $line = ' '.substr($line, strlen($chunk));
        }

        $lines[] = $line;

        return $lines;
    }

    public static function utc(DateTimeInterface $date): string
    {
        return DateTimeImmutable::createFromInterface($date)
            ->setTimezone(new DateTimeZone('UTC'))
            ->format('Ymd\THis\Z');
    }

    public static function date(DateTimeInterface $date): string
    {
        return $date->format('Ymd');
    }
}
