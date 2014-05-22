<?php

namespace Model;

/**
 * LDAP model
 *
 * @package  model
 * @author   Frederic Guillot
 */
class Ldap extends Base
{
    /**
     * Authenticate a user
     *
     * @access public
     * @param  string  $username  Username
     * @param  string  $password  Password
     * @return null|boolean
     */
    public function authenticate($username, $password)
    {
        if (! function_exists('ldap_connect')) {
            die('The PHP LDAP extension is required');
        }

        $ldap = ldap_connect(LDAP_SERVER, LDAP_PORT);

        if (! is_resource($ldap)) {
            die('Unable to connect to the LDAP server: "'.LDAP_SERVER.'"');
        }

        ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);

        if (@ldap_bind($ldap, sprintf(LDAP_USER_DN, $username), $password)) {
            return $this->create($username);
        }

        return false;
    }

    /**
     * Create automatically a new local user after the LDAP authentication
     *
     * @access public
     * @param  string  $username  Username
     * @return bool
     */
    public function create($username)
    {
        $userModel = new User($this->db, $this->event);
        $user = $userModel->getByUsername($username);

        // There is an existing user account
        if ($user) {

            if ($user['is_ldap_user'] == 1) {

                // LDAP user already created
                return true;
            }
            else {

                // There is already a local user with that username
                return false;
            }
        }

        // Create a LDAP user
        $values = array(
            'username' => $username,
            'is_admin' => 0,
            'is_ldap_user' => 1,
        );

        return $userModel->create($values);
    }
}
