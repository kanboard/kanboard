<?php

namespace Kanboard\Core\User;

/**
 * User Provider Interface
 *
 * @package  user
 * @author   Frederic Guillot
 */
interface UserProviderInterface
{
    /**
     * Return true to allow automatic user creation
     *
     * @access public
     * @return boolean
     */
    public function isUserCreationAllowed();

    /**
     * Get external id column name
     *
     * Example: google_id, github_id, gitlab_id...
     *
     * @access public
     * @return string
     */
    public function getExternalIdColumn();

    /**
     * Get internal id
     *
     * If a value is returned the user properties won't be updated in the local database
     *
     * @access public
     * @return integer
     */
    public function getInternalId();

    /**
     * Get external id
     *
     * @access public
     * @return string
     */
    public function getExternalId();

    /**
     * Get user role
     *
     * Return an empty string to not override role stored in the database
     *
     * @access public
     * @return string
     */
    public function getRole();

    /**
     * Get username
     *
     * @access public
     * @return string
     */
    public function getUsername();

    /**
     * Get user full name
     *
     * @access public
     * @return string
     */
    public function getName();

    /**
     * Get user email
     *
     * @access public
     * @return string
     */
    public function getEmail();

    /**
     * Get external group ids
     *
     * A synchronization is done at login time,
     * the user will be member of those groups if they exists in the database
     *
     * @access public
     * @return string[]
     */
    public function getExternalGroupIds();

    /**
     * Get extra user attributes
     *
     * Example: is_ldap_user, disable_login_form, notifications_enabled...
     *
     * @access public
     * @return array
     */
    public function getExtraAttributes();
}
