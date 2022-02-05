<?php

declare(strict_types=1);

namespace Base32;

/**
 * Base32 encoder and decoder.
 *
 * RFC 4648 compliant
 *
 * @see     http://www.ietf.org/rfc/rfc4648.txt
 * Some groundwork based on this class
 * https://github.com/NTICompass/PHP-Base32
 *
 * @author  Christian Riesen <chris.riesen@gmail.com>
 * @author  Sam Williams <sam@badcow.co>
 *
 * @see     http://christianriesen.com
 *
 * @license MIT License see LICENSE file
 */
class Base32
{
    /**
     * Alphabet for encoding and decoding base32.
     *
     * @var string
     */
    protected const ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567=';

    protected const BASE32HEX_PATTERN = '/[^A-Z2-7]/';

    /**
     * Maps the Base32 character to its corresponding bit value.
     */
    protected const MAPPING = [
        '=' => 0b00000,
        'A' => 0b00000,
        'B' => 0b00001,
        'C' => 0b00010,
        'D' => 0b00011,
        'E' => 0b00100,
        'F' => 0b00101,
        'G' => 0b00110,
        'H' => 0b00111,
        'I' => 0b01000,
        'J' => 0b01001,
        'K' => 0b01010,
        'L' => 0b01011,
        'M' => 0b01100,
        'N' => 0b01101,
        'O' => 0b01110,
        'P' => 0b01111,
        'Q' => 0b10000,
        'R' => 0b10001,
        'S' => 0b10010,
        'T' => 0b10011,
        'U' => 0b10100,
        'V' => 0b10101,
        'W' => 0b10110,
        'X' => 0b10111,
        'Y' => 0b11000,
        'Z' => 0b11001,
        '2' => 0b11010,
        '3' => 0b11011,
        '4' => 0b11100,
        '5' => 0b11101,
        '6' => 0b11110,
        '7' => 0b11111,
    ];

    /**
     * Encodes into base32.
     *
     * @param string $string Clear text string
     *
     * @return string Base32 encoded string
     */
    public static function encode(string $string): string
    {
        // Empty string results in empty string
        if ('' === $string) {
            return '';
        }

        $encoded = '';

        //Set the initial values
        $n = $bitLen = $val = 0;
        $len = \strlen($string);

        //Pad the end of the string - this ensures that there are enough zeros
        $string .= \str_repeat(\chr(0), 4);

        //Explode string into integers
        $chars = (array) \unpack('C*', $string, 0);

        while ($n < $len || 0 !== $bitLen) {
            //If the bit length has fallen below 5, shift left 8 and add the next character.
            if ($bitLen < 5) {
                $val = $val << 8;
                $bitLen += 8;
                $n++;
                $val += $chars[$n];
            }
            $shift = $bitLen - 5;
            $encoded .= ($n - (int)($bitLen > 8) > $len && 0 == $val) ? '=' : static::ALPHABET[$val >> $shift];
            $val = $val & ((1 << $shift) - 1);
            $bitLen -= 5;
        }

        return $encoded;
    }

    /**
     * Decodes base32.
     *
     * @param string $base32String Base32 encoded string
     *
     * @return string Clear text string
     */
    public static function decode(string $base32String): string
    {
        // Only work in upper cases
        $base32String = \strtoupper($base32String);

        // Remove anything that is not base32 alphabet
        $base32String = \preg_replace(static::BASE32HEX_PATTERN, '', $base32String);

        // Empty string results in empty string
        if ('' === $base32String || null === $base32String) {
            return '';
        }

        $decoded = '';

        //Set the initial values
        $len = \strlen($base32String);
        $n = 0;
        $bitLen = 5;
        $val = static::MAPPING[$base32String[0]];

        while ($n < $len) {
            //If the bit length has fallen below 8, shift left 5 and add the next pentet.
            if ($bitLen < 8) {
                $val = $val << 5;
                $bitLen += 5;
                $n++;
                $pentet = $base32String[$n] ?? '=';

                //If the new pentet is padding, make this the last iteration.
                if ('=' === $pentet) {
                    $n = $len;
                }
                $val += static::MAPPING[$pentet];
                continue;
            }
            $shift = $bitLen - 8;

            $decoded .= \chr($val >> $shift);
            $val = $val & ((1 << $shift) - 1);
            $bitLen -= 8;
        }

        return $decoded;
    }
}
