<?php

namespace Kanboard\Core\Ldap;

/**
 * LDAP Client
 *
 * @package ldap
 * @author  Frederic Guillot
 */
class Client
{
    /**
     * Get server connection
     *
     * @access public
     * @param  string   $server  LDAP server hostname or IP
     * @param  integer  $port    LDAP port
     * @param  boolean  $tls     Start TLS
     * @param  boolean  $verify  Skip SSL certificate verification
     * @return resource
     */
    public function getConnection($server, $port = LDAP_PORT, $tls = LDAP_START_TLS, $verify = LDAP_SSL_VERIFY)
    {
        if (! function_exists('ldap_connect')) {
            throw new ClientException('LDAP: The PHP LDAP extension is required');
        }

        if (! $verify) {
            putenv('LDAPTLS_REQCERT=never');
        }

        $ldap = ldap_connect($server, $port);

        if ($ldap === false) {
            throw new ClientException('LDAP: Unable to connect to the LDAP server');
        }

        ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
        ldap_set_option($ldap, LDAP_OPT_NETWORK_TIMEOUT, 1);
        ldap_set_option($ldap, LDAP_OPT_TIMELIMIT, 1);

        if ($tls && ! @ldap_start_tls($ldap)) {
            throw new ClientException('LDAP: Unable to start TLS');
        }

        return $ldap;
    }

    /**
     * Anonymous authentication
     *
     * @access public
     * @param  resource $ldap
     * @return boolean
     */
    public function useAnonymousAuthentication($ldap)
    {
        if (! ldap_bind($ldap)) {
            throw new ClientException('Unable to perform anonymous binding');
        }

        return true;
    }

    /**
     * Authentication with username/password
     *
     * @access public
     * @param  resource $ldap
     * @param  string   $username
     * @param  string   $password
     * @return boolean
     */
    public function authenticate($ldap, $username, $password)
    {
        if (! ldap_bind($ldap, $username, $password)) {
            throw new ClientException('Unable to perform anonymous binding');
        }

        return true;
    }
}
