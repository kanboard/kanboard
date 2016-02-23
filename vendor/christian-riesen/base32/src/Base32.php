<?php
namespace Base32;

/**
 * Base32 encoder and decoder
 *
 * Last update: 2012-06-20
 *
 * RFC 4648 compliant
 * @link http://www.ietf.org/rfc/rfc4648.txt
 *
 * Some groundwork based on this class
 * https://github.com/NTICompass/PHP-Base32
 *
 * @author Christian Riesen <chris.riesen@gmail.com>
 * @link http://christianriesen.com
 * @license MIT License see LICENSE file
 */
class Base32
{
    /**
     * Alphabet for encoding and decoding base32
     *
     * @var array
     */
    private static $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567=';

    /**
     * Creates an array from a binary string into a given chunk size
     *
     * @param string $binaryString String to chunk
     * @param integer $bits Number of bits per chunk
     * @return array
     */
    private static function chunk($binaryString, $bits)
    {
        $binaryString = chunk_split($binaryString, $bits, ' ');

        if (substr($binaryString, (strlen($binaryString)) - 1)  == ' ') {
            $binaryString = substr($binaryString, 0, strlen($binaryString)-1);
        }

        return explode(' ', $binaryString);
    }

    /**
     * Encodes into base32
     *
     * @param string $string Clear text string
     * @return string Base32 encoded string
     */
    public static function encode($string)
    {
        if (strlen($string) == 0) {
            // Gives an empty string

            return '';
        }

        // Convert string to binary
        $binaryString = '';

        foreach (str_split($string) as $s) {
            // Return each character as an 8-bit binary string
            $s = decbin(ord($s));
            $binaryString .= str_pad($s, 8, 0, STR_PAD_LEFT);
        }

        // Break into 5-bit chunks, then break that into an array
        $binaryArray = self::chunk($binaryString, 5);

        // Pad array to be divisible by 8
        while (count($binaryArray) % 8 !== 0) {
            $binaryArray[] = null;
        }

        $base32String = '';

        // Encode in base32
        foreach ($binaryArray as $bin) {
            $char = 32;

            if (!is_null($bin)) {
                // Pad the binary strings
                $bin = str_pad($bin, 5, 0, STR_PAD_RIGHT);
                $char = bindec($bin);
            }

            // Base32 character
            $base32String .= substr(self::$alphabet, $char, 1);
        }

        return $base32String;
    }

    /**
     * Decodes base32
     *
     * @param string $base32String Base32 encoded string
     * @return string Clear text string
     */
    public static function decode($base32String)
    {
        // Only work in upper cases
        $base32String = strtoupper($base32String);

        // Remove anything that is not base32 alphabet
        $pattern = '/[^A-Z2-7]/';

        $base32String = preg_replace($pattern, '', $base32String);

        if (strlen($base32String) == 0) {
            // Gives an empty string
            return '';
        }

        $base32Array = str_split($base32String);

        $string = '';

        foreach ($base32Array as $str) {
            $char = strpos(self::$alphabet, $str);

            // Ignore the padding character
            if ($char !== 32) {
                $char = decbin($char);
                $string .= str_pad($char, 5, 0, STR_PAD_LEFT);
            }
        }

        while (strlen($string) %8 !== 0) {
            $string = substr($string, 0, strlen($string)-1);
        }

        $binaryArray = self::chunk($string, 8);

        $realString = '';

        foreach ($binaryArray as $bin) {
            // Pad each value to 8 bits
            $bin = str_pad($bin, 8, 0, STR_PAD_RIGHT);
            // Convert binary strings to ASCII
            $realString .= chr(bindec($bin));
        }

        return $realString;
    }
}
