<?php

namespace Kanboard\Auth;

use Kanboard\Core\Base;
use Kanboard\Event\AuthEvent;

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
     * Get LDAP server name
     *
     * @access public
     * @return string
     */
    public function getLdapServer()
    {
        return LDAP_SERVER;
    }

    /**
     * Get LDAP bind type
     *
     * @access public
     * @return integer
     */
    public function getLdapBindType()
    {
        return LDAP_BIND_TYPE;
    }

    /**
     * Get LDAP server port
     *
     * @access public
     * @return integer
     */
    public function getLdapPort()
    {
        return LDAP_PORT;
    }

    /**
     * Get LDAP username (proxy auth)
     *
     * @access public
     * @return string
     */
    public function getLdapUsername()
    {
        return LDAP_USERNAME;
    }

    /**
     * Get LDAP password (proxy auth)
     *
     * @access public
     * @return string
     */
    public function getLdapPassword()
    {
        return LDAP_PASSWORD;
    }

    /**
     * Get LDAP Base DN
     *
     * @access public
     * @return string
     */
    public function getLdapBaseDn()
    {
        return LDAP_ACCOUNT_BASE;
    }

    /**
     * Get LDAP account id attribute
     *
     * @access public
     * @return string
     */
    public function getLdapAccountId()
    {
        return LDAP_ACCOUNT_ID;
    }

    /**
     * Get LDAP account email attribute
     *
     * @access public
     * @return string
     */
    public function getLdapAccountEmail()
    {
        return LDAP_ACCOUNT_EMAIL;
    }

    /**
     * Get LDAP account name attribute
     *
     * @access public
     * @return string
     */
    public function getLdapAccountName()
    {
        return LDAP_ACCOUNT_FULLNAME;
    }

    /**
     * Get LDAP account memberof attribute
     *
     * @access public
     * @return string
     */
    public function getLdapAccountMemberOf()
    {
        return LDAP_ACCOUNT_MEMBEROF;
    }

    /**
     * Get LDAP admin group DN
     *
     * @access public
     * @return string
     */
    public function getLdapGroupAdmin()
    {
        return LDAP_GROUP_ADMIN_DN;
    }

    /**
     * Get LDAP project admin group DN
     *
     * @access public
     * @return string
     */
    public function getLdapGroupProjectAdmin()
    {
        return LDAP_GROUP_PROJECT_ADMIN_DN;
    }

    /**
     * Get LDAP username pattern
     *
     * @access public
     * @param  string  $username
     * @return string
     */
    public function getLdapUserPattern($username)
    {
        return sprintf(LDAP_USER_PATTERN, $username);
    }

    /**
     * Return true if the LDAP username is case sensitive
     *
     * @access public
     * @return boolean
     */
    public function isLdapAccountCaseSensitive()
    {
        return LDAP_USERNAME_CASE_SENSITIVE;
    }

    /**
     * Return true if the automatic account creation is enabled
     *
     * @access public
     * @return boolean
     */
    public function isLdapAccountCreationEnabled()
    {
        return LDAP_ACCOUNT_CREATION;
    }

    /**
     * Ge the list of attributes to fetch when reading the LDAP user entry
     *
     * Must returns array with index that start at 0 otherwise ldap_search returns a warning "Array initialization wrong"
     *
     * @access public
     * @return array
     */
    public function getProfileAttributes()
    {
        return array_values(array_filter(array(
            $this->getLdapAccountId(),
            $this->getLdapAccountName(),
            $this->getLdapAccountEmail(),
            $this->getLdapAccountMemberOf()
        )));
    }

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
        $username = $this->isLdapAccountCaseSensitive() ? $username : strtolower($username);
        $result = $this->findUser($username, $password);

        if (is_array($result)) {
            $user = $this->user->getByUsername($username);

            if (! empty($user)) {

                // There is already a local user with that name
                if ($user['is_ldap_user'] == 0) {
                    return false;
                }
            } else {

                // We create automatically a new user
                if ($this->isLdapAccountCreationEnabled() && $this->user->create($result) !== false) {
                    $user = $this->user->getByUsername($username);
                } else {
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
     * @return resource|boolean
     */
    public function connect()
    {
        if (! function_exists('ldap_connect')) {
            $this->logger->error('LDAP: The PHP LDAP extension is required');
            return false;
        }

        // Skip SSL certificate verification
        if (! LDAP_SSL_VERIFY) {
            putenv('LDAPTLS_REQCERT=never');
        }

        $ldap = ldap_connect($this->getLdapServer(), $this->getLdapPort());

        if ($ldap === false) {
            $this->logger->error('LDAP: Unable to connect to the LDAP server');
            return false;
        }

        ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
        ldap_set_option($ldap, LDAP_OPT_NETWORK_TIMEOUT, 1);
        ldap_set_option($ldap, LDAP_OPT_TIMELIMIT, 1);

        if (LDAP_START_TLS && ! @ldap_start_tls($ldap)) {
            $this->logger->error('LDAP: Unable to use ldap_start_tls()');
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
     * @return boolean
     */
    public function bind($ldap, $username, $password)
    {
        if ($this->getLdapBindType() === 'user') {
            $ldap_username = sprintf($this->getLdapUsername(), $username);
            $ldap_password = $password;
        } elseif ($this->getLdapBindType() === 'proxy') {
            $ldap_username = $this->getLdapUsername();
            $ldap_password = $this->getLdapPassword();
        } else {
            $ldap_username = null;
            $ldap_password = null;
        }

        if (! @ldap_bind($ldap, $ldap_username, $ldap_password)) {
            $this->logger->error('LDAP: Unable to bind to server with: '.$ldap_username);
            $this->logger->error('LDAP: bind type='.$this->getLdapBindType());
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
     * @return boolean|array
     */
    public function getProfile($ldap, $username, $password)
    {
        $user_pattern = $this->getLdapUserPattern($username);
        $entries = $this->executeQuery($ldap, $user_pattern);

        if ($entries === false) {
            $this->logger->error('LDAP: Unable to get user profile: '.$user_pattern);
            return false;
        }

        if (@ldap_bind($ldap, $entries[0]['dn'], $password)) {
            return $this->prepareProfile($ldap, $entries, $username);
        }

        if (DEBUG) {
            $this->logger->debug('LDAP: wrong password for '.$entries[0]['dn']);
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
        if ($this->getLdapAccountId() !== '') {
            $username = $this->getEntry($entries, $this->getLdapAccountId(), $username);
        }

        return array(
            'username' => $username,
            'name' => $this->getEntry($entries, $this->getLdapAccountName()),
            'email' => $this->getEntry($entries, $this->getLdapAccountEmail()),
            'is_admin' => (int) $this->isMemberOf($this->getEntries($entries, $this->getLdapAccountMemberOf()), $this->getLdapGroupAdmin()),
            'is_project_admin' => (int) $this->isMemberOf($this->getEntries($entries, $this->getLdapAccountMemberOf()), $this->getLdapGroupProjectAdmin()),
            'is_ldap_user' => 1,
        );
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
     * Retrieve info on LDAP user by username or email
     *
     * @access public
     * @param  string  $username
     * @param  string  $email
     * @return boolean|array
     */
    public function lookup($username = null, $email = null)
    {
        $query = $this->getLookupQuery($username, $email);
        if ($query === '') {
            return false;
        }

        // Connect and attempt anonymous or proxy binding
        $ldap = $this->connect();
        if ($ldap === false || ! $this->bind($ldap, null, null)) {
            return false;
        }

        // Try to find user
        $entries = $this->executeQuery($ldap, $query);
        if ($entries === false) {
            return false;
        }

        // User id not retrieved: LDAP_ACCOUNT_ID not properly configured
        if (empty($username) && ! isset($entries[0][$this->getLdapAccountId()][0])) {
            return false;
        }

        return $this->prepareProfile($ldap, $entries, $username);
    }

    /**
     * Execute LDAP query
     *
     * @access private
     * @param  resource  $ldap
     * @param  string    $query
     * @return boolean|array
     */
    private function executeQuery($ldap, $query)
    {
        $sr = @ldap_search($ldap, $this->getLdapBaseDn(), $query, $this->getProfileAttributes());
        if ($sr === false) {
            return false;
        }

        $entries = ldap_get_entries($ldap, $sr);
        if ($entries === false || count($entries) === 0 || $entries['count'] == 0) {
            return false;
        }

        return $entries;
    }

    /**
     * Get the LDAP query to find a user
     *
     * @access private
     * @param  string   $username
     * @param  string   $email
     * @return string
     */
    private function getLookupQuery($username, $email)
    {
        if (! empty($username) && ! empty($email)) {
            return '(&('.$this->getLdapUserPattern($username).')('.$this->getLdapAccountEmail().'='.$email.'))';
        } elseif (! empty($username)) {
            return $this->getLdapUserPattern($username);
        } elseif (! empty($email)) {
            return '('.$this->getLdapAccountEmail().'='.$email.')';
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
