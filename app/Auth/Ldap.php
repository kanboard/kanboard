<?php

namespace Auth;

use Event\AuthEvent;

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
        $username = LDAP_USERNAME_CASE_SENSITIVE ? $username : strtolower($username);
        $result = $this->findUser($username, $password);

        if (is_array($result)) {

            $user = $this->user->getByUsername($username);

            if (! empty($user)) {

                // There is already a local user with that name
                if ($user['is_ldap_user'] == 0) {
                    return false;
                }
            }
            else {

                // We create automatically a new user
                if (LDAP_ACCOUNT_CREATION && $this->user->create($result) !== false) {
                    $user = $this->user->getByUsername($username);
                }
                else {
                    return false;
                }
            }

            // We open the session
            $this->userSession->refresh($user);
            $this->container['dispatcher']->dispatch('auth.success', new AuthEvent(self::AUTH_NAME, $user['id']));

            return true;
        }

        return false;
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

        if ($ldap !== false && $this->bind($ldap, $username, $password)) {
            return $this->getProfile($ldap, $username, $password);
        }

        return false;
    }

    /**
     * LDAP connection
     *
     * @access public
     * @param  string   $ldap_hostname
     * @param  integer  $ldap_port
     * @return resource|boolean
     */
    public function connect($ldap_hostname = LDAP_SERVER, $ldap_port = LDAP_PORT)
    {
        if (! function_exists('ldap_connect')) {
            $this->logger->error('The PHP LDAP extension is required');
            return false;
        }

        // Skip SSL certificate verification
        if (! LDAP_SSL_VERIFY) {
            putenv('LDAPTLS_REQCERT=never');
        }

        $ldap = ldap_connect($ldap_hostname, $ldap_port);

        if ($ldap === false) {
            $this->logger->error('Unable to connect to the LDAP server: "'.LDAP_SERVER.'"');
            return false;
        }

        ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
        ldap_set_option($ldap, LDAP_OPT_NETWORK_TIMEOUT, 1);
        ldap_set_option($ldap, LDAP_OPT_TIMELIMIT, 1);

        if (LDAP_START_TLS && ! @ldap_start_tls($ldap)) {
            $this->logger->error('Unable to use ldap_start_tls()');
            return false;
        }

        return $ldap;
    }

    /**
     * LDAP authentication
     *
     * @access public
     * @param  resource  $ldap
     * @param  string    $username
     * @param  string    $password
     * @param  string    $ldap_type
     * @param  string    $ldap_username
     * @param  string    $ldap_password
     * @return boolean
     */
    public function bind($ldap, $username, $password, $ldap_type = LDAP_BIND_TYPE, $ldap_username = LDAP_USERNAME, $ldap_password = LDAP_PASSWORD)
    {
        if ($ldap_type === 'user') {
            $ldap_username = sprintf($ldap_username, $username);
            $ldap_password = $password;
        }
        else if ($ldap_type === 'proxy') {
            $ldap_username = $ldap_username;
            $ldap_password = $ldap_password;
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
     * Get LDAP user profile
     *
     * @access public
     * @param  resource  $ldap
     * @param  string    $username
     * @param  string    $password
     * @param  string    $base_dn
     * @param  string    $user_pattern
     * @return boolean|array
     */
    public function getProfile($ldap, $username, $password, $base_dn = LDAP_ACCOUNT_BASE, $user_pattern = LDAP_USER_PATTERN)
    {
        $sr = ldap_search($ldap, $base_dn, sprintf($user_pattern, $username), $this->getProfileAttributes());

        if ($sr === false) {
            return false;
        }

        $entries = ldap_get_entries($ldap, $sr);

        if ($entries === false || count($entries) === 0 || $entries['count'] == 0) {
            return false;
        }

        if (@ldap_bind($ldap, $entries[0]['dn'], $password)) {
            return $this->prepareProfile($ldap, $entries, $username);
        }

        return false;
    }

    /**
     * Build user profile from LDAP information
     *
     * @access public
     * @param  resource  $ldap
     * @param  array     $entries
     * @param  string    $username
     * @return boolean|array
     */
    public function prepareProfile($ldap, array $entries, $username)
    {
        return array(
            'username' => $username,
            'name' => $this->getEntry($entries, LDAP_ACCOUNT_FULLNAME),
            'email' => $this->getEntry($entries, LDAP_ACCOUNT_EMAIL),
            'is_admin' => (int) $this->isMemberOf($this->getEntries($entries, LDAP_ACCOUNT_MEMBEROF), LDAP_GROUP_ADMIN_DN),
            'is_project_admin' => (int) $this->isMemberOf($this->getEntries($entries, LDAP_ACCOUNT_MEMBEROF), LDAP_GROUP_PROJECT_ADMIN_DN),
            'is_ldap_user' => 1,
        );
    }

    /**
     * Ge the list of attributes to fetch when reading the LDAP user entry
     *
     * @access public
     * @return array
     */
    public function getProfileAttributes()
    {
        return array(LDAP_ACCOUNT_FULLNAME, LDAP_ACCOUNT_EMAIL, LDAP_ACCOUNT_MEMBEROF);
    }

    /**
     * Check group membership
     *
     * @access public
     * @param  array   $group_entries
     * @param  string  $group_dn
     * @return boolean
     */
    public function isMemberOf(array $group_entries, $group_dn)
    {
        if (! isset($group_entries['count']) || empty($group_dn)) {
            return false;
        }

        for ($i = 0; $i < $group_entries['count']; $i++) {
            if ($group_entries[$i] === $group_dn) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieve info on LDAP user
     *
     * @access public
     * @param  string   $username  Username
     * @param  string   $email     Email address
     * @return boolean|array
     */
    public function lookup($username = null, $email = null)
    {
        $query = $this->getQuery($username, $email);
        if ($query === '') {
            return false;
        }

        // Connect and attempt anonymous bind
        $ldap = $this->connect();
        if ($ldap === false || ! $this->bind($ldap, null, null, 'anonymous')) {
            return false;
        }

        // Try to find user
        $sr = ldap_search($ldap, LDAP_ACCOUNT_BASE, $query, array(LDAP_ACCOUNT_FULLNAME, LDAP_ACCOUNT_EMAIL, LDAP_ACCOUNT_ID));
        if ($sr === false) {
            return false;
        }

        $info = ldap_get_entries($ldap, $sr);

        // User not found
        if (count($info) == 0 || $info['count'] == 0) {
            return false;
        }

        // User id not retrieved: LDAP_ACCOUNT_ID not properly configured
        if (empty($username) && ! isset($info[0][LDAP_ACCOUNT_ID][0])) {
            return false;
        }

        return array(
            'username' => $this->getEntry($info, LDAP_ACCOUNT_ID, $username),
            'name' => $this->getEntry($info, LDAP_ACCOUNT_FULLNAME),
            'email' => $this->getEntry($info, LDAP_ACCOUNT_EMAIL, $email),
        );
    }

    /**
     * Get the LDAP query to find a user
     *
     * @access private
     * @param  string   $username  Username
     * @param  string   $email     Email address
     * @return string
     */
    private function getQuery($username, $email)
    {
        if ($username && $email) {
            return '(&('.sprintf(LDAP_USER_PATTERN, $username).')('.LDAP_ACCOUNT_EMAIL.'='.$email.'))';
        }
        else if ($username) {
            return sprintf(LDAP_USER_PATTERN, $username);
        }
        else if ($email) {
            return '('.LDAP_ACCOUNT_EMAIL.'='.$email.')';
        }

        return '';
    }

    /**
     * Return one entry from a list of entries
     *
     * @access private
     * @param  array    $entries     LDAP entries
     * @param  string   $key         Key
     * @param  string   $default     Default value if key not set in entry
     * @return string
     */
    private function getEntry(array $entries, $key, $default = '')
    {
         return isset($entries[0][$key][0]) ? $entries[0][$key][0] : $default;
    }

    /**
     * Return subset of entries
     *
     * @access private
     * @param  array    $entries
     * @param  string   $key
     * @param  array    $default
     * @return array
     */
    private function getEntries(array $entries, $key, $default = array())
    {
         return isset($entries[0][$key]) ? $entries[0][$key] : $default;
    }
}
