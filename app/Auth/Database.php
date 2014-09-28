<?php

namespace Auth;

use Model\User;
use Core\Request;

/**
 * Database authentication
 *
 * @package  auth
 * @author   Frederic Guillot
 */
class Database extends Base
{
    /**
     * Backend name
     *
     * @var string
     */
    const AUTH_NAME = 'Database';

    /**
     * Authenticate a user
     *
     * @access public
     * @param  string  $username  Username
     * @param  string  $password  Password
     * @return boolean
     */
    public function authenticate($username, $password)
    {
        $user = $this->db->table(User::TABLE)->eq('username', $username)->eq('is_ldap_user', 0)->findOne();

        if ($user && password_verify($password, $user['password'])) {

            // Update user session
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
}
