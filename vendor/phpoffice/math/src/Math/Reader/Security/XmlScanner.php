<?php

namespace PhpOffice\Math\Reader\Security;

use PhpOffice\Math\Exception\SecurityException;

class XmlScanner
{
    public static function getInstance(): self
    {
        return new self();
    }

    /**
     * Scan the XML for use of <!ENTITY to prevent XXE/XEE attacks.
     */
    public function scan(string $xml): string
    {
        // Don't rely purely on libxml_disable_entity_loader()
        $searchDoctype = static::mb_str_split('<!DOCTYPE', 1, 'UTF-8');
        $patternDoctype = '/\0*' . implode('\0*', is_array($searchDoctype) ? $searchDoctype : []) . '\0*/';
        $searchDoctypeMath = static::mb_str_split('<!DOCTYPE math', 1, 'UTF-8');
        $patternDoctypeMath = '/\0*' . implode('\0*', is_array($searchDoctypeMath) ? $searchDoctypeMath : []) . '\0*/';

        if (preg_match($patternDoctype, $xml) && !preg_match($patternDoctypeMath, $xml)) {
            throw new SecurityException('Detected use of ENTITY in XML, loading aborted to prevent XXE/XEE attacks');
        }

        return $xml;
    }

    /**
     * @param string $string
     * @param int<1, max> $split_length
     * @param string|null $encoding
     *
     * @return array<string>|bool|null
     */
    public static function mb_str_split(string $string, int $split_length = 1, ?string $encoding = null)
    {
        if (extension_loaded('mbstring')) {
            if (function_exists('mb_str_split')) {
                return mb_str_split($string, $split_length, $encoding);
            }
        }
        // @phpstan-ignore-next-line
        if (null !== $string && !\is_scalar($string) && !(\is_object($string) && method_exists($string, '__toString'))) {
            trigger_error('mb_str_split() expects parameter 1 to be string, ' . \gettype($string) . ' given', \E_USER_WARNING);

            return null;
        }

        // @phpstan-ignore-next-line
        if (1 > $split_length = (int) $split_length) {
            trigger_error('The length of each segment must be greater than zero', \E_USER_WARNING);

            return false;
        }

        if (null === $encoding) {
            $encoding = mb_internal_encoding();
        }

        if ('UTF-8' === $encoding || \in_array(strtoupper($encoding), ['UTF-8', 'UTF8'], true)) {
            return preg_split("/(.{{$split_length}})/u", $string, -1, \PREG_SPLIT_DELIM_CAPTURE | \PREG_SPLIT_NO_EMPTY);
        }

        $result = [];
        $length = mb_strlen($string, $encoding);

        for ($i = 0; $i < $length; $i += $split_length) {
            $result[] = mb_substr($string, $i, $split_length, $encoding);
        }

        return $result;
    }
}
