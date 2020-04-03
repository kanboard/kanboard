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
     * Return subtask list toggle value
     *
     * @access public
     * @return boolean
     */
    public function hasSubtaskListActivated()
    {
        return $this->userSession->hasSubtaskListActivated();
    }

    /**
     * Return true if the logged user has unread notifications
     *
     * @access public
     * @return boolean
     */
    public function hasNotifications()
    {
        return $this->userUnreadNotificationModel->hasNotifications($this->userSession->getId());
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
            $initials .= mb_substr($string, 0, 1, 'UTF-8');
        }

        return mb_strtoupper($initials, 'UTF-8');
    }

    /**
     * Return the user full name
     *
     * @param  array    $user   User properties
     * @return string
     */
    public function getFullname(array $user = array())
    {
        $user = empty($user) ? $this->userSession->getAll() : $user;
        return $user['name'] ?: $user['username'];
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
     * Check if group-memberships should be displayed in user-list for a given user
     * User must be a member of at least one group && CONSTANT(from config.php) to diplay group-memberships in userlist must be true!
     *
     * @access public
     * @param  integer   $user_id   User id
     * @return boolean
     */
    public function getDisplayGroupNamesInUserList($user_id)
    {
        // config-CONSTANT is not mandatory in config.default.php so check to see if it's there ELSE set CONSTANT to default
        defined('SHOW_GROUP_MEMBERSHIPS_IN_USERLIST') or define('SHOW_GROUP_MEMBERSHIPS_IN_USERLIST', true);
        return ( count($this->groupMemberModel->getGroups($user_id)) > 0 && SHOW_GROUP_MEMBERSHIPS_IN_USERLIST );
    }

    /**
     * Get group names(as a comma-separated list) for a given user
     *
     * @access public
     * @param  integer   $user_id   User id
     * @return string
     */
    public function getGroupNames($user_id)
    {
        return implode(', ', array_column($this->groupMemberModel->getGroups($user_id), 'name'));
    }

    /**
     * Get group names(as a comma-separated list) for a given user (limited to N groups depending on value of CONSTANT in config.php )
     *
     * @access public
     * @param  integer   $user_id   User id
     * @return string
     */
    public function getGroupNamesLimited($user_id)
    {
        // config-CONSTANT is not mandatory in config.default.php so check to see if it's there ELSE set CONSTANT to default
        defined('SHOW_GROUP_MEMBERSHIPS_IN_USERLIST_WITH_LIMIT') or define('SHOW_GROUP_MEMBERSHIPS_IN_USERLIST_WITH_LIMIT', 7);
        $full_list = array_column($this->groupMemberModel->getGroups($user_id), 'name');
        // let's reduce the arry to the limit
        $limited_list = ( SHOW_GROUP_MEMBERSHIPS_IN_USERLIST_WITH_LIMIT == 0 ) ? $full_list : array_slice($full_list, 0 , SHOW_GROUP_MEMBERSHIPS_IN_USERLIST_WITH_LIMIT);
        // if limiting had any effect ... let's add a hint to the list, to inform the user there are more group-memberships for that user
        $limited_list = ( $full_list == $limited_list ) ? implode(', ', $limited_list) : implode(', ', $limited_list) . ' ( >> ' . t('hover mouse over group-icon, to show all group-memberships') . ' )';
        return $limited_list;
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
        if (! $this->userSession->isLogged()) {
            return false;
        }

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
        $key = 'project_access:'.$controller.$action.$project_id;
        $result = $this->memoryCache->get($key);

        if ($result === null) {
            $result = $this->helper->projectRole->checkProjectAccess($controller, $action, $project_id);
            $this->memoryCache->set($key, $result);
        }

        return $result;
    }
}
