<?php

namespace Kanboard\Core\Ldap;

use LogicException;

/**
 * LDAP Client
 *
 * @package ldap
 * @author  Frederic Guillot
 */
class Client
{
    /**
     * LDAP resource
     *
     * @access protected
     * @var resource
     */
    protected $ldap;

    /**
     * Establish LDAP connection
     *
     * @static
     * @access public
     * @param  string $username
     * @param  string $password
     * @return Client
     */
    public static function connect($username = null, $password = null)
    {
        $client = new self;
        $client->open($client->getLdapServer());
        $username = $username ?: $client->getLdapUsername();
        $password = $password ?: $client->getLdapPassword();

        if (empty($username) && empty($password)) {
            $client->useAnonymousAuthentication();
        } else {
            $client->authenticate($username, $password);
        }

        return $client;
    }

    /**
     * Get server connection
     *
     * @access public
     * @return resource
     */
    public function getConnection()
    {
        return $this->ldap;
    }

    /**
     * Establish server connection
     *
     * @access public
     * @param  string   $server  LDAP server hostname or IP
     * @param  integer  $port    LDAP port
     * @param  boolean  $tls     Start TLS
     * @param  boolean  $verify  Skip SSL certificate verification
     * @return Client
     */
    public function open($server, $port = LDAP_PORT, $tls = LDAP_START_TLS, $verify = LDAP_SSL_VERIFY)
    {
        if (! function_exists('ldap_connect')) {
            throw new ClientException('LDAP: The PHP LDAP extension is required');
        }

        if (! $verify) {
            putenv('LDAPTLS_REQCERT=never');
        }

        $this->ldap = ldap_connect($server, $port);

        if ($this->ldap === false) {
            throw new ClientException('LDAP: Unable to connect to the LDAP server');
        }

        ldap_set_option($this->ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($this->ldap, LDAP_OPT_REFERRALS, 0);
        ldap_set_option($this->ldap, LDAP_OPT_NETWORK_TIMEOUT, 1);
        ldap_set_option($this->ldap, LDAP_OPT_TIMELIMIT, 1);

        if ($tls && ! @ldap_start_tls($this->ldap)) {
            throw new ClientException('LDAP: Unable to start TLS');
        }

        return $this;
    }

    /**
     * Anonymous authentication
     *
     * @access public
     * @return boolean
     */
    public function useAnonymousAuthentication()
    {
        if (! @ldap_bind($this->ldap)) {
            throw new ClientException('Unable to perform anonymous binding');
        }

        return true;
    }

    /**
     * Authentication with username/password
     *
     * @access public
     * @param  string  $bind_rdn
     * @param  string  $bind_password
     * @return boolean
     */
    public function authenticate($bind_rdn, $bind_password)
    {
        if (! @ldap_bind($this->ldap, $bind_rdn, $bind_password)) {
            throw new ClientException('LDAP authentication failure for "'.$bind_rdn.'"');
        }

        return true;
    }

    /**
     * Get LDAP server name
     *
     * @access public
     * @return string
     */
    public function getLdapServer()
    {
        if (! LDAP_SERVER) {
            throw new LogicException('LDAP server not configured, check the parameter LDAP_SERVER');
        }

        return LDAP_SERVER;
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
}
