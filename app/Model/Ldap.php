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

        // Skip SSL certificate verification
        if (! LDAP_SSL_VERIFY) {
            putenv('LDAPTLS_REQCERT=never');
        }

        $ldap = ldap_connect(LDAP_SERVER, LDAP_PORT);

        if (! is_resource($ldap)) {
            die('Unable to connect to the LDAP server: "'.LDAP_SERVER.'"');
        }

        ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);

        if (! @ldap_bind($ldap, LDAP_USERNAME, LDAP_PASSWORD)) {
            die('Unable to bind to the LDAP server: "'.LDAP_SERVER.'"');
        }

        $sr = @ldap_search($ldap, LDAP_ACCOUNT_BASE, sprintf(LDAP_USER_PATTERN, $username), array(LDAP_ACCOUNT_FULLNAME, LDAP_ACCOUNT_EMAIL));

        if ($sr === false) {
            return false;
        }

        $info = ldap_get_entries($ldap, $sr);

        // User not found
        if (count($info) == 0 || $info['count'] == 0) {
            return false;
        }

        if (@ldap_bind($ldap,  $info[0]['dn'], $password)) {
            return $this->create($username, $info[0][LDAP_ACCOUNT_FULLNAME][0], $info[0][LDAP_ACCOUNT_EMAIL][0]);
        }

        return false;
    }

    /**
     * Create automatically a new local user after the LDAP authentication
     *
     * @access public
     * @param  string  $username  Username
     * @param  string  $name      Name of the user
     * @param  string  $email       Email address
     * @return bool
     */
    public function create($username, $name, $email)
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
            'name' => $name,
            'email' => $email,
            'is_admin' => 0,
            'is_ldap_user' => 1,
        );

        return $userModel->create($values);
    }
}
