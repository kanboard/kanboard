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
     * Get group names for a given user and return an associative array:
     *  ['full_list'] = a comma-separated list of all group-memberships
     *  ['limited_list'] = a comma-separated list limited to N groups depending on value of SHOW_GROUP_MEMBERSHIPS_IN_USERLIST_WITH_LIMIT
     *  ['has_groups'] = boolean TRUE if user is member of at least one group ... else FALSE
     *
     * @access public
     * @param  integer   $user_id   User id
     * @return array
     */
    public function getUsersGroupNames($user_id)
    {
        $groups_list = array_column($this->groupMemberModel->getGroups($user_id), 'name');
        $full_list = implode(', ', $groups_list);

        // let's reduce the array to the limit
        $limited_list = ( SHOW_GROUP_MEMBERSHIPS_IN_USERLIST_WITH_LIMIT == 0 ) ? $groups_list : array_slice($groups_list, 0 , SHOW_GROUP_MEMBERSHIPS_IN_USERLIST_WITH_LIMIT);
        // if limiting had any effect ... let's add a hint to the list, to inform the user there are more group-memberships for that user
        $limited_list = ( $groups_list == $limited_list ) ? implode(', ', $limited_list) : implode(', ', $limited_list) . ' ( >> ' . t('hover mouse over group-icon, to show all group-memberships') . ' )';

        $has_groups = (count($groups_list)) ? true : false;

        return array(
            'full_list' => $full_list,
            'limited_list' => $limited_list,
            'has_groups' => $has_groups
        );
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
