<?php

namespace Core;

/**
 * Session class
 *
 * @package  core
 * @author   Frederic Guillot
 */
class Session
{
    /**
     * Sesion lifetime
     *
     * http://php.net/manual/en/session.configuration.php#ini.session.cookie-lifetime
     *
     * @var integer
     */
    const SESSION_LIFETIME = 0; // Until the browser is closed

    /**
     * Return true if the session is open
     *
     * @static
     * @access public
     * @return boolean
     */
    public static function isOpen()
    {
        return session_id() !== '';
    }

    /**
     * Open a session
     *
     * @access public
     * @param  string   $base_path    Cookie path
     */
    public function open($base_path = '/')
    {
        // HttpOnly and secure flags for session cookie
        session_set_cookie_params(
            self::SESSION_LIFETIME,
            $base_path ?: '/',
            null,
            Request::isHTTPS(),
            true
        );

        // Avoid session id in the URL
        ini_set('session.use_only_cookies', '1');

        // Enable strict mode
        ini_set('session.use_strict_mode', '1');

        // Ensure session ID integrity
        ini_set('session.entropy_file', '/dev/urandom');
        ini_set('session.entropy_length', '32');
        ini_set('session.hash_bits_per_character', 6);

        // If session was autostarted with session.auto_start = 1 in php.ini destroy it
        if (isset($_SESSION)) {
            session_destroy();
        }

        // Custom session name
        session_name('__S');

        // Start the session
        session_start();

        // Regenerate the session id to avoid session fixation issue
        if (empty($_SESSION['__validated'])) {
            session_regenerate_id(true);
            $_SESSION['__validated'] = 1;
        }
    }

    /**
     * Destroy the session
     *
     * @access public
     */
    public function close()
    {
        // Flush all sessions variables
        $_SESSION = array();

        // Destroy the session cookie
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        // Destroy session data
        session_destroy();
    }

    /**
     * Register a flash message (success notification)
     *
     * @access public
     * @param  string   $message   Message
     */
    public function flash($message)
    {
        $_SESSION['flash_message'] = $message;
    }

    /**
     * Register a flash error message (error notification)
     *
     * @access public
     * @param  string   $message   Message
     */
    public function flashError($message)
    {
        $_SESSION['flash_error_message'] = $message;
    }
}
