<?php

namespace Kanboard\Core\Session;

/**
 * Session Storage
 *
 * @package  session
 * @author   Frederic Guillot
 *
 * @property array  $user
 * @property array  $flash
 * @property array  $csrf
 * @property array  $postAuthenticationValidated
 * @property array  $filters
 * @property string $redirectAfterLogin
 * @property string $captcha
 * @property string $commentSorting
 * @property bool   $hasSubtaskInProgress
 * @property bool   $hasRememberMe
 * @property bool   $subtaskListToggle
 * @property string $scope
 * @property bool   $twoFactorBeforeCodeCalled
 * @property string $twoFactorSecret
 * @property string $oauthState
 * @property int    $smsTwoFactorSecret
 */
class SessionStorage
{
    /**
     * Pointer to external storage
     *
     * @access private
     * @var array
     */
    private $storage = array();

    /**
     * Set external storage
     *
     * @access public
     * @param  array  $storage  External session storage (example: $_SESSION)
     */
    public function setStorage(array &$storage)
    {
        $this->storage =& $storage;

        // Load dynamically existing session variables into object properties
        foreach ($storage as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Get all session variables
     *
     * @access public
     * @return array
     */
    public function getAll()
    {
        $session = get_object_vars($this);
        unset($session['storage']);

        return $session;
    }

    /**
     * Flush session data
     *
     * @access public
     */
    public function flush()
    {
        $session = get_object_vars($this);
        unset($session['storage']);

        foreach (array_keys($session) as $property) {
            unset($this->$property);
        }
    }

    /**
     * Copy class properties to external storage
     *
     * @access public
     */
    public function __destruct()
    {
        $this->storage = $this->getAll();
    }
}
