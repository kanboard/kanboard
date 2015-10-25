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
    /**
     * Generate a random token with different methods: openssl or /dev/urandom or fallback to uniqid()
     *
     * @static
     * @access public
     * @return string  Random token
     */
    public static function getToken()
    {
        if (function_exists('openssl_random_pseudo_bytes')) {
            return bin2hex(\openssl_random_pseudo_bytes(30));
        } elseif (ini_get('open_basedir') === '' && strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
            return hash('sha256', file_get_contents('/dev/urandom', false, null, 0, 30));
        }

        return hash('sha256', uniqid(mt_rand(), true));
    }

    /**
     * Generate and store a CSRF token in the current session
     *
     * @access public
     * @return string  Random token
     */
    public function getCSRFToken()
    {
        if (! isset($_SESSION['csrf_tokens'])) {
            $_SESSION['csrf_tokens'] = array();
        }

        $nonce = self::getToken();
        $_SESSION['csrf_tokens'][$nonce] = true;

        return $nonce;
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
        if (isset($_SESSION['csrf_tokens'][$token])) {
            unset($_SESSION['csrf_tokens'][$token]);
            return true;
        }

        return false;
    }
}
