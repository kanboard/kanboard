<?php

namespace Kanboard\User;

use Kanboard\Core\User\UserProviderInterface;

/**
 * OAuth User Provider
 *
 * @package  user
 * @author   Frederic Guillot
 */
abstract class OAuthUserProvider implements UserProviderInterface
{
    /**
     * User properties
     *
     * @access protected
     * @var array
     */
    protected $user = array();

    /**
     * Constructor
     *
     * @access public
     * @param  array $user
     */
    public function __construct(array $user)
    {
        $this->user = $user;
    }

    /**
     * Return true to allow automatic user creation
     *
     * @access public
     * @return boolean
     */
    public function isUserCreationAllowed()
    {
        return false;
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
     * Get external id
     *
     * @access public
     * @return string
     */
    public function getExternalId()
    {
        return isset($this->user['id']) ? $this->user['id'] : '';
    }

    /**
     * Get user role
     *
     * @access public
     * @return string
     */
    public function getRole()
    {
        return '';
    }

    /**
     * Get username
     *
     * @access public
     * @return string
     */
    public function getUsername()
    {
        return '';
    }

    /**
     * Get full name
     *
     * @access public
     * @return string
     */
    public function getName()
    {
        return isset($this->user['name']) ? $this->user['name'] : '';
    }

    /**
     * Get user email
     *
     * @access public
     * @return string
     */
    public function getEmail()
    {
        return isset($this->user['email']) ? $this->user['email'] : '';
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
        return array();
    }
}
