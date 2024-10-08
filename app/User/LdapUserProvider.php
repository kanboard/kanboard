<?php

namespace Kanboard\User;

use Kanboard\Core\User\UserProviderInterface;
use Kanboard\Model\LanguageModel;

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
     * @access protected
     * @var string
     */
    protected $dn;

    /**
     * LDAP username
     *
     * @access protected
     * @var string
     */
    protected $username;

    /**
     * User name
     *
     * @access protected
     * @var string
     */
    protected $name;

    /**
     * Email
     *
     * @access protected
     * @var string
     */
    protected $email;

    /**
     * User role
     *
     * @access protected
     * @var string
     */
    protected $role;

    /**
     * Group LDAP DNs
     *
     * @access protected
     * @var string[]
     */
    protected $groupIds;

    /**
     * User photo
     *
     * @access protected
     * @var string
     */
    protected $photo = '';

    /**
     * User language
     *
     * @access protected
     * @var string
     */
    protected $language = '';

    /**
     * Constructor
     *
     * @access public
     * @param  string   $dn
     * @param  string   $username
     * @param  string   $name
     * @param  string   $email
     * @param  string   $role
     * @param  string[] $groupIds
     * @param  string   $photo
     * @param  string   $language
     */
    public function __construct($dn, $username, $name, $email, $role, array $groupIds, $photo = '', $language = '')
    {
        $this->dn = $dn;
        $this->username = $username;
        $this->name = $name;
        $this->email = $email;
        $this->role = $role;
        $this->groupIds = $groupIds;
        $this->photo = $photo;
        $this->language = $language;
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
     * @return integer
     */
    public function getInternalId()
    {
        return 0;
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
     * Get groups DN
     *
     * @access public
     * @return string[]
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
        $attributes = array('is_ldap_user' => 1);

        if (! empty($this->language)) {
            $attributes['language'] = LanguageModel::findCode($this->language);
        }

        return $attributes;
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

    /**
     * Get user photo
     *
     * @access public
     * @return string
     */
    public function getPhoto()
    {
        return $this->photo;
    }
}
