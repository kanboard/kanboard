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
     * The credentials fingerprint is preserved: an ordinary refresh must never revalidate
     * a session opened before the password changed. Only the session that changed the
     * password gets the new fingerprint.
     *
     * @access public
     * @param integer $user_id
     * @param boolean $renewCredentialsFingerprint
     */
    public function refresh($user_id, $renewCredentialsFingerprint = false)
    {
        if ($this->getId() == $user_id) {
            $fingerprint = $this->getCredentialsFingerprint();
            $this->initialize($this->userModel->getById($user_id));

            if (! $renewCredentialsFingerprint) {
                session_merge('user', array('credentials_fingerprint' => $fingerprint));
            }
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
        // Keep a fingerprint of the password to invalidate the session when the credentials change
        $fingerprint = hash('sha256', isset($user['password']) ? $user['password'] : '');

        foreach (array('password', 'is_admin', 'is_project_admin', 'twofactor_secret') as $column) {
            if (isset($user[$column])) {
                unset($user[$column]);
            }
        }

        $user['id'] = (int) $user['id'];
        $user['credentials_fingerprint'] = $fingerprint;
        $user['is_ldap_user'] = isset($user['is_ldap_user']) ? (bool) $user['is_ldap_user'] : false;
        $user['twofactor_activated'] = isset($user['twofactor_activated']) ? (bool) $user['twofactor_activated'] : false;

        if (session_status() === PHP_SESSION_ACTIVE) {
            // Note: Do not delete the old session to avoid possible race condition and a PHP warning.
            session_regenerate_id(false);
        }

        session_set('user', $user);
        session_set('postAuthenticationValidated', false);
    }

    /**
     * Get the credentials fingerprint stored in the session
     *
     * Returns an empty string for sessions created before this value was stored,
     * which never matches a real fingerprint and forces a new authentication.
     *
     * @access public
     * @return string
     */
    public function getCredentialsFingerprint()
    {
        $user = session_get('user');
        return isset($user['credentials_fingerprint']) ? $user['credentials_fingerprint'] : '';
    }

    /**
     * Get user properties
     *
     * @access public
     * @return array
     */
    public function getAll()
    {
        return session_get('user');
    }

    /**
     * Get user application role
     *
     * @access public
     * @return string
     */
    public function getRole()
    {
        if (! $this->isLogged()) {
            return '';
        }

        return session_get('user')['role'];
    }

    /**
     * Return true if the user has validated the 2FA key
     *
     * @access public
     * @return bool
     */
    public function isPostAuthenticationValidated()
    {
        return session_is_true('postAuthenticationValidated');
    }

    /**
     * Validate 2FA for the current session
     *
     * @access public
     */
    public function setPostAuthenticationAsValidated()
    {
        session_set('postAuthenticationValidated', true);
    }

    /**
     * Return true if the user has 2FA enabled
     *
     * @access public
     * @return bool
     */
    public function hasPostAuthentication()
    {
        if (! $this->isLogged()) {
            return false;
        }

        return session_get('user')['twofactor_activated'] === true;
    }

    /**
     * Disable 2FA for the current session
     *
     * @access public
     */
    public function disablePostAuthentication()
    {
        session_merge('user', ['twofactor_activated' => false]);
    }

    /**
     * Return true if the logged user is admin
     *
     * @access public
     * @return bool
     */
    public function isAdmin()
    {
        return $this->getRole() === Role::APP_ADMIN;
    }

    /**
     * Get the connected user id
     *
     * @access public
     * @return integer
     */
    public function getId()
    {
        if (! $this->isLogged()) {
            return 0;
        }

        return session_get('user')['id'];
    }

    /**
     * Get username
     *
     * @access public
     * @return string
     */
    public function getUsername()
    {
        if (! $this->isLogged()) {
            return '';
        }

        return session_get('user')['username'];
    }

    /**
     * Get user language
     *
     * @access public
     * @return string
     */
    public function getLanguage()
    {
        if (! $this->isLogged()) {
            return '';
        }

        return session_get('user')['language'];
    }

    /**
     * Get user timezone
     *
     * @access public
     * @return string
     */
    public function getTimezone()
    {
        if (! $this->isLogged()) {
            return '';
        }

        return session_get('user')['timezone'];
    }

    /**
     * Get user theme
     *
     * @access public
     * @return string
     */
    public function getTheme()
    {
        if (! $this->isLogged()) {
            return 'light';
        }

        $user_session = session_get('user');

        if (array_key_exists('theme', $user_session)) {
            return $user_session['theme'];
        }

        return 'light';
    }

    /**
     * Return true if subtask list toggle is active
     *
     * @access public
     * @return string
     */
    public function hasSubtaskListActivated()
    {
        return session_is_true('subtaskListToggle');
    }

    /**
     * Check is the user is connected
     *
     * @access public
     * @return bool
     */
    public function isLogged()
    {
        return session_exists('user') && session_get('user') !== [];
    }

    /**
     * Get project filters from the session
     *
     * @access public
     * @param  integer  $projectID
     * @return string
     */
    public function getFilters($projectID)
    {
        if (! session_exists('filters:'.$projectID)) {
            return session_get('user') ? session_get('user')['filter'] ?: 'status:open' : 'status:open';
        }

        return session_get('filters:'.$projectID);
    }

    /**
     * Save project filters in the session
     *
     * @access public
     * @param  integer  $projectID
     * @param  string   $filters
     */
    public function setFilters($projectID, $filters)
    {
        session_set('filters:'.$projectID, $filters);
    }

    /**
     * Get project list order from the session
     *
     * @access public
     * @param  integer  $projectID
     * @return array
     */
    public function getListOrder($projectID)
    {
        $default = ['tasks.id', 'DESC'];

        if (! session_exists('listOrder:'.$projectID)) {
            return $default;
        }

        return session_get('listOrder:'.$projectID);
    }

    /**
     * Save project list order in the session
     *
     * @access public
     * @param  integer  $projectID
     * @param  string   $listOrder
     * @param  string   $listDirection
     */
    public function setListOrder($projectID, $listOrder, $listDirection)
    {
        session_set('listOrder:'.$projectID, [$listOrder, $listDirection]);
    }
}
