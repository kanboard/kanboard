<?php

namespace Auth;

use Model\User;
use Event\AuthEvent;

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
            $this->userSession->refresh($user);
            $this->container['dispatcher']->dispatch('auth.success', new AuthEvent(self::AUTH_NAME, $user['id']));
            return true;
        }

        return false;
    }
}
