<?php

namespace Auth;

use Core\Request;

/**
 * LDAP model
 *
 * @package  auth
 * @author   Frederic Guillot
 */
class Ldap extends Base
{
    /**
     * Backend name
     *
     * @var string
     */
    const AUTH_NAME = 'LDAP';

    /**
     * Authenticate the user
     *
     * @access public
     * @param  string  $username  Username
     * @param  string  $password  Password
     * @return boolean
     */
    public function authenticate($username, $password)
    {
        $result = $this->findUser($username, $password);

        if (is_array($result)) {

            $user = $this->user->getByUsername($username);

            if ($user) {

                // There is already a local user with that name
                if ($user['is_ldap_user'] == 0) {
                    return false;
                }
            }
            else {

                // We create automatically a new user
                if ($this->createUser($username, $result['name'], $result['email'])) {
                    $user = $this->user->getByUsername($username);
                }
                else {
                    return false;
                }
            }

            // We open the session
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
     * Create a new local user after the LDAP authentication
     *
     * @access public
     * @param  string  $username    Username
     * @param  string  $name        Name of the user
     * @param  string  $email       Email address
     * @return bool
     */
    public function createUser($username, $name, $email)
    {
        $values = array(
            'username' => $username,
            'name' => $name,
            'email' => $email,
            'is_admin' => 0,
            'is_ldap_user' => 1,
        );

        return $this->user->create($values);
    }

    /**
     * Find the user from the LDAP server
     *
     * @access public
     * @param  string  $username  Username
     * @param  string  $password  Password
     * @return boolean|array
     */
    public function findUser($username, $password)
    {
        $ldap = $this->connect();

        if (is_resource($ldap) && $this->bind($ldap, $username, $password)) {
            return $this->search($ldap, $username, $password);
        }

        return false;
    }

    /**
     * LDAP connection
     *
     * @access private
     * @return resource    $ldap    LDAP connection
     */
    private function connect()
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
        ldap_set_option($ldap, LDAP_OPT_NETWORK_TIMEOUT, 1);
        ldap_set_option($ldap, LDAP_OPT_TIMELIMIT, 1);

        if (LDAP_START_TLS && ! @ldap_start_tls($ldap)) {
            die('Unable to use ldap_start_tls()');
        }

        return $ldap;
    }

    /**
     * LDAP bind
     *
     * @access private
     * @param  resource  $ldap      LDAP connection
     * @param  string    $username  Username
     * @param  string    $password  Password
     * @return boolean
     */
    private function bind($ldap, $username, $password)
    {
        if (LDAP_BIND_TYPE === 'user') {
            $ldap_username = sprintf(LDAP_USERNAME, $username);
            $ldap_password = $password;
        }
        else if (LDAP_BIND_TYPE === 'proxy') {
            $ldap_username = LDAP_USERNAME;
            $ldap_password = LDAP_PASSWORD;
        }
        else {
            $ldap_username = null;
            $ldap_password = null;
        }

        if (! @ldap_bind($ldap, $ldap_username, $ldap_password)) {
            return false;
        }

        return true;
    }

    /**
     * LDAP user lookup
     *
     * @access private
     * @param  resource  $ldap      LDAP connection
     * @param  string    $username  Username
     * @param  string    $password  Password
     * @return boolean|array
     */
    private function search($ldap, $username, $password)
    {
        $sr = @ldap_search($ldap, LDAP_ACCOUNT_BASE, sprintf(LDAP_USER_PATTERN, $username), array(LDAP_ACCOUNT_FULLNAME, LDAP_ACCOUNT_EMAIL));

        if ($sr === false) {
            return false;
        }

        $info = ldap_get_entries($ldap, $sr);

        // User not found
        if (count($info) == 0 || $info['count'] == 0) {
            return false;
        }

        // We got our user
        if (@ldap_bind($ldap, $info[0]['dn'], $password)) {

            return array(
                'username' => $username,
                'name' => isset($info[0][LDAP_ACCOUNT_FULLNAME][0]) ? $info[0][LDAP_ACCOUNT_FULLNAME][0] : '',
                'email' => isset($info[0][LDAP_ACCOUNT_EMAIL][0]) ? $info[0][LDAP_ACCOUNT_EMAIL][0] : '',
            );
        }

        return false;
    }
}
