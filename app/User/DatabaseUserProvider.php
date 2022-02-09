<?php

namespace Kanboard\User;

use Kanboard\Core\User\UserProviderInterface;

/**
 * Database User Provider
 *
 * @package  user
 * @author   Frederic Guillot
 */
class DatabaseUserProvider implements UserProviderInterface
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
        return $this->user['id'];
    }

    /**
     * Get external id column name
     *
     * @access public
     * @return string
     */
    public function getExternalIdColumn()
    {
        return '';
    }

    /**
     * Get external id
     *
     * @access public
     * @return string
     */
    public function getExternalId()
    {
        return '';
    }

    /**
     * Get user role
     *
     * @access public
     * @return string
     */
    public function getRole()
    {
        return empty($this->user['role']) ? '' : $this->user['role'];
    }

    /**
     * Get username
     *
     * @access public
     * @return string
     */
    public function getUsername()
    {
        return empty($this->user['username']) ? '' : $this->user['username'];
    }

    /**
     * Get full name
     *
     * @access public
     * @return string
     */
    public function getName()
    {
        return empty($this->user['name']) ? '' : $this->user['name'];
    }

    /**
     * Get user email
     *
     * @access public
     * @return string
     */
    public function getEmail()
    {
        return empty($this->user['email']) ? '' : $this->user['email'];
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
