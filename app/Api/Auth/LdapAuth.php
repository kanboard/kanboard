<?php

namespace Kanboard\Auth;

use LogicException;
use Kanboard\Core\Base;
use Kanboard\Core\Ldap\Client as LdapClient;
use Kanboard\Core\Ldap\ClientException as LdapException;
use Kanboard\Core\Ldap\User as LdapUser;
use Kanboard\Core\Security\PasswordAuthenticationProviderInterface;

/**
 * LDAP Authentication Provider
 *
 * @package  Kanboard\Auth
 * @author   Frederic Guillot
 */
class LdapAuth extends Base implements PasswordAuthenticationProviderInterface
{
    /**
     * User properties
     *
     * @access protected
     * @var \Kanboard\User\LdapUserProvider
     */
    protected $userInfo = null;

    /**
     * Username
     *
     * @access protected
     * @var string
     */
    protected $username = '';

    /**
     * Password
     *
     * @access protected
     * @var string
     */
    protected $password = '';

    /**
     * Get authentication provider name
     *
     * @access public
     * @return string
     */
    public function getName()
    {
        return 'LDAP';
    }

    /**
     * Authenticate the user
     *
     * @access public
     * @return boolean
     */
    public function authenticate()
    {
        try {

            $client = LdapClient::connect($this->getLdapUsername(), $this->getLdapPassword());
            $client->setLogger($this->logger);

            $user = LdapUser::getUser($client, $this->username);

            if ($user === null) {
                $this->logger->info('User ('.$this->username.') not found in LDAP server');
                return false;
            }

            if ($user->getUsername() === '') {
                throw new LogicException('Username not found in LDAP profile, check the parameter LDAP_USER_ATTRIBUTE_USERNAME');
            }

            $this->logger->info('Authenticate this user: '.$user->getDn());

            if ($client->authenticate($user->getDn(), $this->password)) {
                $this->userInfo = $user;
                return true;
            }

        } catch (LdapException $e) {
            $this->logger->error($e->getMessage());
        }

        return false;
    }

    /**
     * Get user object
     *
     * @access public
     * @return \Kanboard\User\LdapUserProvider
     */
    public function getUser()
    {
        return $this->userInfo;
    }

    /**
     * Set username
     *
     * @access public
     * @param  string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Set password
     *
     * @access public
     * @param  string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Get LDAP username (proxy auth)
     *
     * @access public
     * @return string
     */
    public function getLdapUsername()
    {
        switch ($this->getLdapBindType()) {
            case 'proxy':
                return LDAP_USERNAME;
            case 'user':
                return sprintf(LDAP_USERNAME, $this->username);
            default:
                return null;
        }
    }

    /**
     * Get LDAP password (proxy auth)
     *
     * @access public
     * @return string
     */
    public function getLdapPassword()
    {
        switch ($this->getLdapBindType()) {
            case 'proxy':
                return LDAP_PASSWORD;
            case 'user':
                return $this->password;
            default:
                return null;
        }
    }

    /**
     * Get LDAP bind type
     *
     * @access public
     * @return integer
     */
    public function getLdapBindType()
    {
        if (LDAP_BIND_TYPE !== 'user' && LDAP_BIND_TYPE !== 'proxy' && LDAP_BIND_TYPE !== 'anonymous') {
            throw new LogicException('Wrong value for the parameter LDAP_BIND_TYPE');
        }

        return LDAP_BIND_TYPE;
    }
}
