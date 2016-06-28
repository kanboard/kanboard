<?php

namespace Kanboard\Core\User;

use Kanboard\Core\Base;
use Kanboard\Core\Security\Role;

/**
 * User Session
 *
 * @package  user
 * @author   Frederic Guillot
 */
class UserSession extends Base
{
    /**
     * Refresh current session if necessary
     *
     * @access public
     * @param integer $user_id
     */
    public function refresh($user_id)
    {
        if ($this->getId() == $user_id) {
            $this->initialize($this->userModel->getById($user_id));
        }
    }

    /**
     * Update user session
     *
     * @access public
     * @param  array  $user
     */
    public function initialize(array $user)
    {
        foreach (array('password', 'is_admin', 'is_project_admin', 'twofactor_secret') as $column) {
            if (isset($user[$column])) {
                unset($user[$column]);
            }
        }

        $user['id'] = (int) $user['id'];
        $user['is_ldap_user'] = isset($user['is_ldap_user']) ? (bool) $user['is_ldap_user'] : false;
        $user['twofactor_activated'] = isset($user['twofactor_activated']) ? (bool) $user['twofactor_activated'] : false;

        $this->sessionStorage->user = $user;
        $this->sessionStorage->postAuthenticationValidated = false;
    }

    /**
     * Get user properties
     *
     * @access public
     * @return array
     */
    public function getAll()
    {
        return $this->sessionStorage->user;
    }

    /**
     * Get user application role
     *
     * @access public
     * @return string
     */
    public function getRole()
    {
        return $this->sessionStorage->user['role'];
    }

    /**
     * Return true if the user has validated the 2FA key
     *
     * @access public
     * @return bool
     */
    public function isPostAuthenticationValidated()
    {
        return isset($this->sessionStorage->postAuthenticationValidated) && $this->sessionStorage->postAuthenticationValidated === true;
    }

    /**
     * Validate 2FA for the current session
     *
     * @access public
     */
    public function validatePostAuthentication()
    {
        $this->sessionStorage->postAuthenticationValidated = true;
    }

    /**
     * Return true if the user has 2FA enabled
     *
     * @access public
     * @return bool
     */
    public function hasPostAuthentication()
    {
        return isset($this->sessionStorage->user['twofactor_activated']) && $this->sessionStorage->user['twofactor_activated'] === true;
    }

    /**
     * Disable 2FA for the current session
     *
     * @access public
     */
    public function disablePostAuthentication()
    {
        $this->sessionStorage->user['twofactor_activated'] = false;
    }

    /**
     * Return true if the logged user is admin
     *
     * @access public
     * @return bool
     */
    public function isAdmin()
    {
        return isset($this->sessionStorage->user['role']) && $this->sessionStorage->user['role'] === Role::APP_ADMIN;
    }
public function isUser()
    {
        return isset($this->sessionStorage->user['role']) && (($this->sessionStorage->user['role'] === Role::APP_USER)or ($this->sessionStorage->user['role'] === Role::APP_ADMIN) or ($this->sessionStorage->user['role'] === Role::APP_MANAGER));
    }
    /**
     * Get the connected user id
     *
     * @access public
     * @return integer
     */
    public function getId()
    {
        return isset($this->sessionStorage->user['id']) ? (int) $this->sessionStorage->user['id'] : 0;
    }

    /**
     * Get username
     *
     * @access public
     * @return string
     */
    public function getUsername()
    {
        return isset($this->sessionStorage->user['username']) ? $this->sessionStorage->user['username'] : '';
    }

    /**
     * Check is the user is connected
     *
     * @access public
     * @return bool
     */
    public function isLogged()
    {
        return isset($this->sessionStorage->user) && ! empty($this->sessionStorage->user);
    }

    /**
     * Get project filters from the session
     *
     * @access public
     * @param  integer  $project_id
     * @return string
     */
    public function getFilters($project_id)
    {
        return ! empty($this->sessionStorage->filters[$project_id]) ? $this->sessionStorage->filters[$project_id] : 'status:open';
    }

    /**
     * Save project filters in the session
     *
     * @access public
     * @param  integer  $project_id
     * @param  string   $filters
     */
    public function setFilters($project_id, $filters)
    {
        $this->sessionStorage->filters[$project_id] = $filters;
    }

    /**
     * Is board collapsed or expanded
     *
     * @access public
     * @param  integer  $project_id
     * @return boolean
     */
    public function isBoardCollapsed($project_id)
    {
        return ! empty($this->sessionStorage->boardCollapsed[$project_id]) ? $this->sessionStorage->boardCollapsed[$project_id] : false;
    }

    /**
     * Set board display mode
     *
     * @access public
     * @param  integer  $project_id
     * @param  boolean  $is_collapsed
     */
    public function setBoardDisplayMode($project_id, $is_collapsed)
    {
        $this->sessionStorage->boardCollapsed[$project_id] = $is_collapsed;
    }

    /**
     * Set comments sorting
     *
     * @access public
     * @param  string $order
     */
    public function setCommentSorting($order)
    {
        $this->sessionStorage->commentSorting = $order;
    }

    /**
     * Get comments sorting direction
     *
     * @access public
     * @return string
     */
    public function getCommentSorting()
    {
        return empty($this->sessionStorage->commentSorting) ? 'ASC' : $this->sessionStorage->commentSorting;
    }
}
