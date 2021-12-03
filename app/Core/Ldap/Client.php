<?php

namespace Kanboard\Core\Ldap;

use LogicException;
use Psr\Log\LoggerInterface;

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
     * Logger instance
     *
     * @access private
     * @var LoggerInterface
     */
    private $logger;

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
        $client = new static;
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
     *
     * @param  string $server LDAP server URI (ldap[s]://hostname:port) or hostname (deprecated) 
     * @param  int    $port   LDAP port (deprecated)
     * @param  bool   $tls    Start TLS
     * @param  bool   $verify Skip SSL certificate verification
     * @return Client
     * @throws ClientException
     * @throws ConnectionException
     */
    public function open($server, $port = LDAP_PORT, $tls = LDAP_START_TLS, $verify = LDAP_SSL_VERIFY)
    {
        if (! function_exists('ldap_connect')) {
            throw new ClientException('LDAP: The PHP LDAP extension is required');
        }

        if (! $verify) {
            putenv('LDAPTLS_REQCERT=never');
        }

        if (filter_var($server, FILTER_VALIDATE_URL) !== false) {
            $this->ldap = @ldap_connect($server);
        }
        else {
            $this->ldap = @ldap_connect($server, $port);
        }

        if ($this->ldap === false) {
            throw new ConnectionException('Malformed LDAP server hostname or LDAP server port');
        }

        ldap_set_option($this->ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($this->ldap, LDAP_OPT_REFERRALS, 0);
        ldap_set_option($this->ldap, LDAP_OPT_NETWORK_TIMEOUT, 1);
        ldap_set_option($this->ldap, LDAP_OPT_TIMELIMIT, 1);

        if ($tls && ! @ldap_start_tls($this->ldap)) {
            throw new ConnectionException('Unable to start LDAP TLS (' . $this->getLdapError() . ')');
        }

        return $this;
    }

    /**
     * Anonymous authentication
     *
     * @access public
     * @throws ClientException
     * @return boolean
     */
    public function useAnonymousAuthentication()
    {
        if (! @ldap_bind($this->ldap)) {
            $this->checkForServerConnectionError();
            throw new ClientException('Unable to perform anonymous binding => '.$this->getLdapError());
        }

        return true;
    }

    /**
     * Authentication with username/password
     *
     * @access public
     * @throws ClientException
     * @param  string  $bind_rdn
     * @param  string  $bind_password
     * @return boolean
     */
    public function authenticate($bind_rdn, $bind_password)
    {
        if (! @ldap_bind($this->ldap, $bind_rdn, $bind_password)) {
            $this->checkForServerConnectionError();
            throw new ClientException('LDAP authentication failure for "'.$bind_rdn.'" => '.$this->getLdapError());
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

    /**
     * Set logger
     *
     * @access public
     * @param  LoggerInterface $logger
     * @return Client
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
        return $this;
    }

    /**
     * Get logger
     *
     * @access public
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Test if a logger is defined
     *
     * @access public
     * @return boolean
     */
    public function hasLogger()
    {
        return $this->logger !== null;
    }

    /**
     * Raise ConnectionException if the application is not able to connect to LDAP server
     *
     * @access protected
     * @throws ConnectionException
     */
    protected function checkForServerConnectionError()
    {
        if (ldap_errno($this->ldap) === -1) {
            throw new ConnectionException('Unable to connect to LDAP server (' . $this->getLdapError() . ')');
        }
    }

    /**
     * Get extended LDAP error message
     *
     * @return string
     */
    protected function getLdapError()
    {
        ldap_get_option($this->ldap, LDAP_OPT_ERROR_STRING, $extendedErrorMessage);
        $errorMessage = ldap_error($this->ldap);
        $errorCode = ldap_errno($this->ldap);

        return 'Code="'.$errorCode.'"; Error="'.$errorMessage.'"; ExtendedError="'.$extendedErrorMessage.'"';
    }
}
