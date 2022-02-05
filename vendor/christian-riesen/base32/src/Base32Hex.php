<?php

declare(strict_types=1);

namespace Base32;

/**
 * Base32Hex encoder and decoder.
 *
 * RFC 4648 compliant
 * @see     http://www.ietf.org/rfc/rfc4648.txt
 *
 * @author  Sam Williams <sam@badcow.co>
 *
 * @see     http://christianriesen.com
 *
 * @license MIT License see LICENSE file
 */
class Base32Hex extends Base32
{
    /**
     * Alphabet for encoding and decoding base32 extended hex.
     *
     * @var string
     */
    protected const ALPHABET = '0123456789ABCDEFGHIJKLMNOPQRSTUV=';

    protected const BASE32HEX_PATTERN = '/[^0-9A-V]/';

    /**
     * Maps the Base32 character to its corresponding bit value.
     */
    protected const MAPPING = [
        '=' => 0b00000,
        '0' => 0b00000,
        '1' => 0b00001,
        '2' => 0b00010,
        '3' => 0b00011,
        '4' => 0b00100,
        '5' => 0b00101,
        '6' => 0b00110,
        '7' => 0b00111,
        '8' => 0b01000,
        '9' => 0b01001,
        'A' => 0b01010,
        'B' => 0b01011,
        'C' => 0b01100,
        'D' => 0b01101,
        'E' => 0b01110,
        'F' => 0b01111,
        'G' => 0b10000,
        'H' => 0b10001,
        'I' => 0b10010,
        'J' => 0b10011,
        'K' => 0b10100,
        'L' => 0b10101,
        'M' => 0b10110,
        'N' => 0b10111,
        'O' => 0b11000,
        'P' => 0b11001,
        'Q' => 0b11010,
        'R' => 0b11011,
        'S' => 0b11100,
        'T' => 0b11101,
        'U' => 0b11110,
        'V' => 0b11111,
    ];
}
