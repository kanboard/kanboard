<?php

namespace Kanboard\Helper;

/**
 * User helpers
 *
 * @package helper
 * @author  Frederic Guillot
 */
class User extends \Kanboard\Core\Base
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

        foreach (explode(' ', $name) as $string) {
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
     * Get user profile
     *
     * @access public
     * @return string
     */
    public function getProfileLink()
    {
        return $this->helper->url->link(
            $this->helper->e($this->getFullname()),
            'user',
            'show',
            array('user_id' => $this->userSession->getId())
        );
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
     * Return if the logged user is project admin
     *
     * @access public
     * @return boolean
     */
    public function isProjectAdmin()
    {
        return $this->userSession->isProjectAdmin();
    }

    /**
     * Check for project administration actions access (Project Admin group)
     *
     * @access public
     * @return boolean
     */
    public function isProjectAdministrationAllowed($project_id)
    {
        if ($this->userSession->isAdmin()) {
            return true;
        }

        return $this->memoryCache->proxy($this->container['acl'], 'handleProjectAdminPermissions', $project_id);
    }

    /**
     * Check for project management actions access (Regular users who are Project Managers)
     *
     * @access public
     * @return boolean
     */
    public function isProjectManagementAllowed($project_id)
    {
        if ($this->userSession->isAdmin()) {
            return true;
        }

        return $this->memoryCache->proxy($this->container['acl'], 'handleProjectManagerPermissions', $project_id);
    }

    /**
     * Return the user full name
     *
     * @param  array    $user   User properties
     * @return string
     */
    public function getFullname(array $user = array())
    {
        return $this->user->getFullname(empty($user) ? $_SESSION['user'] : $user);
    }

    /**
     * Display gravatar image
     *
     * @access public
     * @param  string  $email
     * @param  string  $alt
     * @return string
     */
    public function avatar($email, $alt = '')
    {
        if (! empty($email) && $this->config->get('integration_gravatar') == 1) {
            return '<img class="avatar" src="https://www.gravatar.com/avatar/'.md5(strtolower($email)).'?s=25" alt="'.$this->helper->e($alt).'" title="'.$this->helper->e($alt).'">';
        }

        return '';
    }
}
