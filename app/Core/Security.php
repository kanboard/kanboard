<?php

namespace Kanboard\Core;

/**
 * Security class
 *
 * @package  core
 * @author   Frederic Guillot
 */
class Security
{
    /**
     * Generate a random token with different methods: openssl or /dev/urandom or fallback to uniqid()
     *
     * @static
     * @access public
     * @return string  Random token
     */
    public static function generateToken()
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
     * @static
     * @access public
     * @return string  Random token
     */
    public static function getCSRFToken()
    {
        $nonce = self::generateToken();

        if (empty($_SESSION['csrf_tokens'])) {
            $_SESSION['csrf_tokens'] = array();
        }

        $_SESSION['csrf_tokens'][$nonce] = true;

        return $nonce;
    }

    /**
     * Check if the token exists for the current session (a token can be used only one time)
     *
     * @static
     * @access public
     * @param  string   $token   CSRF token
     * @return bool
     */
    public static function validateCSRFToken($token)
    {
        if (isset($_SESSION['csrf_tokens'][$token])) {
            unset($_SESSION['csrf_tokens'][$token]);
            return true;
        }

        return false;
    }

    /**
     * Check if the token used in a form is correct and then remove the value
     *
     * @static
     * @access public
     * @param  array    $values   Form values
     * @return bool
     */
    public static function validateCSRFFormToken(array &$values)
    {
        if (! empty($values['csrf_token']) && self::validateCSRFToken($values['csrf_token'])) {
            unset($values['csrf_token']);
            return true;
        }

        return false;
    }
}
