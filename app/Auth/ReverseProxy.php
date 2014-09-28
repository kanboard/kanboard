<?php

namespace Auth;

use Core\Request;
use Core\Security;

/**
 * ReverseProxy backend
 *
 * @package  auth
 * @author   Sylvain Veyrié
 */
class ReverseProxy extends Base
{
    /**
     * Backend name
     *
     * @var string
     */
    const AUTH_NAME = 'ReverseProxy';

    /**
     * Authenticate the user with the HTTP header
     *
     * @access public
     * @return bool
     */
    public function authenticate()
    {
        if (isset($_SERVER[REVERSE_PROXY_USER_HEADER])) {

            $login = $_SERVER[REVERSE_PROXY_USER_HEADER];
            $user = $this->user->getByUsername($login);

            if (! $user) {
                $this->createUser($login);
                $user = $this->user->getByUsername($login);
            }

            // Create the user session
            $this->user->updateSession($user);

            // Update login history
            $this->lastLogin->create(
                self::AUTH_NAME,
                $user['id'],
                Request::getIpAddress(),
                Request::getUserAgent()
            );

            return true;
        }

        return false;
    }

    /**
     * Create automatically a new local user after the authentication
     *
     * @access private
     * @param  string  $login  Username
     * @return bool
     */
    private function createUser($login)
    {
        $email = strpos($login, '@') !== false ? $login : '';

        if (REVERSE_PROXY_DEFAULT_DOMAIN !== '' && empty($email)) {
            $email = $login.'@'.REVERSE_PROXY_DEFAULT_DOMAIN;
        }

        return $this->user->create(array(
            'email' => $email,
            'username' => $login,
            'is_admin' => REVERSE_PROXY_DEFAULT_ADMIN === $login,
            'is_ldap_user' => 1,
        ));
    }
}
