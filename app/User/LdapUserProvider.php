<?php

namespace Kanboard\User;

use Kanboard\Core\User\UserProviderInterface;

/**
 * LDAP User Provider
 *
 * @package  user
 * @author   Frederic Guillot
 */
class LdapUserProvider implements UserProviderInterface
{
    /**
     * LDAP DN
     *
     * @access private
     * @var string
     */
    private $dn;

    /**
     * LDAP username
     *
     * @access private
     * @var string
     */
    private $username;

    /**
     * User name
     *
     * @access private
     * @var string
     */
    private $name;

    /**
     * Email
     *
     * @access private
     * @var string
     */
    private $email;

    /**
     * User role
     *
     * @access private
     * @var string
     */
    private $role;

    /**
     * Group LDAP DNs
     *
     * @access private
     * @var string[]
     */
    private $groupIds;

    /**
     * Constructor
     *
     * @access public
     * @param  string   $dn
     * @param  string   $username
     * @param  string   $name
     * @param  string   $email
     * @param  string   $role
     * @param  string[]
     */
    public function __construct($dn, $username, $name, $email, $role, array $groupIds)
    {
        $this->dn = $dn;
        $this->username = $username;
        $this->name = $name;
        $this->email = $email;
        $this->role = $role;
        $this->groupIds = $groupIds;
    }

    /**
     * Return true to allow automatic user creation
     *
     * @access public
     * @return boolean
     */
    public function isUserCreationAllowed()
    {
        return LDAP_USER_CREATION;
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
        return $this->getUsername();
    }

    /**
     * Get user role
     *
     * @access public
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Get username
     *
     * @access public
     * @return string
     */
    public function getUsername()
    {
        return LDAP_USERNAME_CASE_SENSITIVE ? $this->username : strtolower($this->username);
    }

    /**
     * Get full name
     *
     * @access public
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get user email
     *
     * @access public
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Get groups
     *
     * @access public
     * @return array
     */
    public function getExternalGroupIds()
    {
        return $this->groupIds;
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
        );
    }

    /**
     * Get User DN
     *
     * @access public
     * @return string
     */
    public function getDn()
    {
        return $this->dn;
    }
}
