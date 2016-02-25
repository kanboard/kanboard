<?php

namespace Kanboard\User;

use Kanboard\Core\User\UserProviderInterface;
use Kanboard\Core\Security\Role;

/**
 * Reverse Proxy User Provider
 *
 * @package  user
 * @author   Frederic Guillot
 */
class ReverseProxyUserProvider implements UserProviderInterface
{
    /**
     * Username
     *
     * @access protected
     * @var string
     */
    protected $username = '';

    /**
     * Constructor
     *
     * @access public
     * @param  string $username
     */
    public function __construct($username)
    {
        $this->username = $username;
    }

    /**
     * Return true to allow automatic user creation
     *
     * @access public
     * @return boolean
     */
    public function isUserCreationAllowed()
    {
        return true;
    }

    /**
     * Get internal id
     *
     * @access public
     * @return string
     */
    public function getInternalId()
    {
        return '';
    }

    /**
     * Get external id column name
     *
     * @access public
     * @return string
     */
    public function getExternalIdColumn()
    {
        return 'username';
    }

    /**
     * Get external id
     *
     * @access public
     * @return string
     */
    public function getExternalId()
    {
        return $this->username;
    }

    /**
     * Get user role
     *
     * @access public
     * @return string
     */
    public function getRole()
    {
        return REVERSE_PROXY_DEFAULT_ADMIN === $this->username ? Role::APP_ADMIN : Role::APP_USER;
    }

    /**
     * Get username
     *
     * @access public
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Get full name
     *
     * @access public
     * @return string
     */
    public function getName()
    {
        return '';
    }

    /**
     * Get user email
     *
     * @access public
     * @return string
     */
    public function getEmail()
    {
        return REVERSE_PROXY_DEFAULT_DOMAIN !== '' ? $this->username.'@'.REVERSE_PROXY_DEFAULT_DOMAIN : '';
    }

    /**
     * Get external group ids
     *
     * @access public
     * @return array
     */
    public function getExternalGroupIds()
    {
        return array();
    }

    /**
     * Get extra user attributes
     *
     * @access public
     * @return array
     */
    public function getExtraAttributes()
    {
        return array(
            'is_ldap_user' => 1,
            'disable_login_form' => 1,
        );
    }
}
