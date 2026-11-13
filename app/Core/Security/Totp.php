<?php

namespace Kanboard\Core\Security;

/**
 * Time-based One-Time Password (RFC 6238, SHA1, 6 digits, 30 seconds)
 *
 * @package  security
 * @author   Frederic Guillot
 */
class Totp
{
    const PERIOD = 30;
    const DIGITS = 6;
    const ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    /**
     * Generate a random base32 encoded secret
     *
     * @access public
     * @param  integer $length
     * @return string
     */
    public function generateSecret(int $length = 16): string
    {
        $secret = '';

        for ($i = 0; $i < $length; $i++) {
            $secret .= self::ALPHABET[random_int(0, 31)];
        }

        return $secret;
    }

    /**
     * Check a code against the current time step and adjacent ones
     *
     * @access public
     * @param  string  $secret     Base32 encoded secret
     * @param  string  $code       Code entered by the user
     * @param  integer $timestamp  Defaults to the current time
     * @param  integer $window     Number of time steps accepted before and after
     * @return boolean
     */
    public function verify(string $secret, string $code, ?int $timestamp = null, int $window = 1): bool
    {
        $key = $this->base32Decode($secret);
        $counter = intdiv($timestamp ?? time(), self::PERIOD);

        for ($i = -$window; $i <= $window; $i++) {
            if (hash_equals($this->hotp($key, $counter + $i), $code)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate an HOTP code (RFC 4226)
     *
     * @access public
     * @param  string  $key      Binary key
     * @param  integer $counter
     * @return string
     */
    public function hotp(string $key, int $counter): string
    {
        $hash = hash_hmac('sha1', pack('J', $counter), $key, true);
        $offset = ord($hash[19]) & 0x0f;
        $value = unpack('N', substr($hash, $offset, 4))[1] & 0x7fffffff;

        return str_pad((string) ($value % 10 ** self::DIGITS), self::DIGITS, '0', STR_PAD_LEFT);
    }

    /**
     * Decode a base32 string (RFC 4648), invalid characters are ignored
     *
     * @access public
     * @param  string $input
     * @return string
     */
    public function base32Decode(string $input): string
    {
        $input = preg_replace('/[^A-Z2-7]/', '', strtoupper($input));
        $length = strlen($input);
        $output = '';
        $buffer = 0;
        $bits = 0;

        for ($i = 0; $i < $length; $i++) {
            $buffer = ($buffer << 5) | strpos(self::ALPHABET, $input[$i]);
            $bits += 5;

            if ($bits >= 8) {
                $bits -= 8;
                $output .= chr(($buffer >> $bits) & 0xff);
                $buffer &= (1 << $bits) - 1;
            }
        }

        return $output;
    }

    /**
     * Get the otpauth:// key URI used by authenticator apps
     *
     * @access public
     * @param  string $label
     * @param  string $secret  Base32 encoded secret
     * @param  string $issuer
     * @return string
     */
    public function getKeyUri(string $label, string $secret, string $issuer): string
    {
        return 'otpauth://totp/'.str_replace(array(':', ' '), array('%3A', '%20'), trim($label)).
            '?secret='.rawurlencode($secret).
            '&issuer='.rawurlencode($issuer);
    }
}
