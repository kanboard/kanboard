<?php

namespace Kanboard\Core\Security;

use Kanboard\Core\Base;

/**
 * Token Handler
 *
 * @package  security
 * @author   Frederic Guillot
 */
class Token extends Base
{
    protected static $KEY_LENGTH = 32;
    protected static $NONCE_LENGTH = 16;
    protected static $HMAC_ALGO = 'sha256';
    protected static $HMAC_LENGTH = 16;

    /**
     * Generate a random token with different methods: openssl or /dev/urandom or fallback to uniqid()
     *
     * @static
     * @access public
     * @return string  Random token
     */
    public static function getToken($length = 30)
    {
        return bin2hex(random_bytes($length));
    }

    /**
     * Generate and store a one-time CSRF token
     *
     * @access public
     * @return string  Random token
     */
    public function getCSRFToken()
    {
        return $this->createSessionToken('csrf');
    }

    /**
     * Generate and store a reusable CSRF token
     *
     * @access public
     * @return string
     */
    public function getReusableCSRFToken()
    {
        return $this->createSessionToken('pcsrf');
    }

    /**
     * Check if the token exists for the current session (a token can be used only one time)
     *
     * @access public
     * @param  string   $token   CSRF token
     * @return bool
     */
    public function validateCSRFToken($token)
    {
        return $this->validateSessionToken('csrf', $token);
    }

    /**
     * Check if the token exists as a reusable CSRF token
     *
     * @access public
     * @param  string   $token   CSRF token
     * @return bool
     */
    public function validateReusableCSRFToken($token)
    {
        return $this->validateSessionToken('pcsrf', $token);
    }

    /**
     * Generate a session token of the given type
     *
     * @access protected
     * @param  string  $type    Token type
     * @return string  Random token
     */
    protected function createSessionToken($type)
    {
        $nonce = self::getToken(self::$NONCE_LENGTH);
        return $nonce . $this->signSessionToken($type, $nonce);
    }

    /**
     * Check a session token of the given type
     *
     * @access protected
     * @param  string   $type    Token type
     * @param  string   $token   Session token
     * @return bool
     */
    protected function validateSessionToken($type, $token)
    {
        if (!is_string($token)) {
            return false;
        }

        if (strlen($token) != (self::$NONCE_LENGTH + self::$HMAC_LENGTH) * 2) {
            return false;
        }

        $nonce = substr($token, 0, self::$NONCE_LENGTH * 2);
        $hmac = substr($token, self::$NONCE_LENGTH * 2, self::$HMAC_LENGTH * 2);

        return hash_equals($this->signSessionToken($type, $nonce), $hmac);
    }

    /**
     * Sign a nonce with the key belonging to the given type
     *
     * @access protected
     * @param  string   $type    Token type
     * @param  string   $nonce   Nonce to sign
     * @return string
     */
    protected function signSessionToken($type, $nonce)
    {
        if (!session_exists($type . '_key')) {
            session_set($type . '_key', self::getToken(self::$KEY_LENGTH));
        }

        $data = $nonce . '-' . session_id();
        $key = session_get($type . '_key');
        $hmac = hash_hmac(self::$HMAC_ALGO, $data, $key, true);

        return bin2hex(substr($hmac, 0, self::$HMAC_LENGTH));
    }
}
