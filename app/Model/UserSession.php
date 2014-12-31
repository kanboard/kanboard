<?php

namespace Model;

/**
 * User Session
 *
 * @package  model
 * @author   Frederic Guillot
 */
class UserSession extends Base
{
    /**
     * Return true if the logged user is admin
     *
     * @access public
     * @return bool
     */
    public function isAdmin()
    {
        return isset($this->session['user']['is_admin']) && $this->session['user']['is_admin'] === true;
    }

    /**
     * Get the connected user id
     *
     * @access public
     * @return integer
     */
    public function getId()
    {
        return isset($this->session['user']['id']) ? (int) $this->session['user']['id'] : 0;
    }

    /**
     * Check if the given user_id is the connected user
     *
     * @param  integer   $user_id   User id
     * @return boolean
     */
    public function isCurrentUser($user_id)
    {
        return $this->getId() == $user_id;
    }

    /**
     * Check is the user is connected
     *
     * @access public
     * @return bool
     */
    public function isLogged()
    {
        return ! empty($this->session['user']);
    }

    /**
     * Get the last seen project from the session
     *
     * @access public
     * @return integer
     */
    public function getLastSeenProjectId()
    {
        return empty($this->session['last_show_project_id']) ? 0 : $this->session['last_show_project_id'];
    }

    /**
     * Get the default project from the session
     *
     * @access public
     * @return integer
     */
    public function getFavoriteProjectId()
    {
        return isset($this->session['user']['default_project_id']) ? $this->session['user']['default_project_id'] : 0;
    }

    /**
     * Set the last seen project from the session
     *
     * @access public
     * @param integer    $project_id    Project id
     */
    public function storeLastSeenProjectId($project_id)
    {
        $this->session['last_show_project_id'] = (int) $project_id;
    }
}
