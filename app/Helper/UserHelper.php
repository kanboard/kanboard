<?php

namespace Kanboard\Helper;

use Kanboard\Core\Base;

/**
 * User helpers
 *
 * @package helper
 * @author  Frederic Guillot
 */
class UserHelper extends Base
{
    /**
     * Return true if the logged user as unread notifications
     *
     * @access public
     * @return boolean
     */
    public function hasNotifications()
    {
        return $this->userUnreadNotification->hasNotifications($this->userSession->getId());
    }

    /**
     * Get initials from a user
     *
     * @access public
     * @param  string  $name
     * @return string
     */
    public function getInitials($name)
    {
        $initials = '';

        foreach (explode(' ', $name, 2) as $string) {
            $initials .= mb_substr($string, 0, 1);
        }

        return mb_strtoupper($initials);
    }

    /**
     * Get user id
     *
     * @access public
     * @return integer
     */
    public function getId()
    {
        return $this->userSession->getId();
    }

    /**
     * Check if the given user_id is the connected user
     *
     * @param  integer   $user_id   User id
     * @return boolean
     */
    public function isCurrentUser($user_id)
    {
        return $this->userSession->getId() == $user_id;
    }

    /**
     * Return if the logged user is admin
     *
     * @access public
     * @return boolean
     */
    public function isAdmin()
    {
        return $this->userSession->isAdmin();
    }

    /**
     * Get role name
     *
     * @access public
     * @param  string  $role
     * @return string
     */
    public function getRoleName($role = '')
    {
        return $this->role->getRoleName($role ?: $this->userSession->getRole());
    }

    /**
     * Check application access
     *
     * @param  string  $controller
     * @param  string  $action
     * @return bool
     */
    public function hasAccess($controller, $action)
    {
        $key = 'app_access:'.$controller.$action;
        $result = $this->memoryCache->get($key);

        if ($result === null) {
            $result = $this->applicationAuthorization->isAllowed($controller, $action, $this->userSession->getRole());
            $this->memoryCache->set($key, $result);
        }

        return $result;
    }

    /**
     * Check project access
     *
     * @param  string  $controller
     * @param  string  $action
     * @param  integer $project_id
     * @return bool
     */
    public function hasProjectAccess($controller, $action, $project_id)
    {
        if ($this->userSession->isAdmin()) {
            return true;
        }

        if (! $this->hasAccess($controller, $action)) {
            return false;
        }

        $key = 'project_access:'.$controller.$action.$project_id;
        $result = $this->memoryCache->get($key);

        if ($result === null) {
            $role = $this->getProjectUserRole($project_id);
            $result = $this->projectAuthorization->isAllowed($controller, $action, $role);
            $this->memoryCache->set($key, $result);
        }

        return $result;
    }

    /**
     * Get project role for the current user
     *
     * @access public
     * @param  integer $project_id
     * @return string
     */
    public function getProjectUserRole($project_id)
    {
        return $this->memoryCache->proxy($this->projectUserRole, 'getUserRole', $project_id, $this->userSession->getId());
    }

    /**
     * Return the user full name
     *
     * @param  array    $user   User properties
     * @return string
     */
    public function getFullname(array $user = array())
    {
        return $this->user->getFullname(empty($user) ? $this->userSession->getAll() : $user);
    }
}
