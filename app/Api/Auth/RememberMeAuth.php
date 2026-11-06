<?php

namespace Kanboard\Auth;

use Kanboard\Core\Base;
use Kanboard\Core\Security\PreAuthenticationProviderInterface;
use Kanboard\User\DatabaseUserProvider;

/**
 * RememberMe Cookie Authentication Provider
 *
 * @package  Kanboard\Auth
 * @author   Frederic Guillot
 */
class RememberMeAuth extends Base implements PreAuthenticationProviderInterface
{
    /**
     * User properties
     *
     * @access protected
     * @var array
     */
    protected $userInfo = array();

    /**
     * Get authentication provider name
     *
     * @access public
     * @return string
     */
    public function getName()
    {
        return 'RememberMe';
    }

    /**
     * Authenticate the user
     *
     * @access public
     * @return boolean
     */
    public function authenticate()
    {
        $credentials = $this->rememberMeCookie->read();

        if ($credentials !== false) {
            $session = $this->rememberMeSessionModel->find($credentials['token'], $credentials['sequence']);

            if (! empty($session)) {
                $this->rememberMeCookie->write(
                    $session['token'],
                    $this->rememberMeSessionModel->updateSequence($session['token']),
                    $session['expiration']
                );

                $this->userInfo = $this->userModel->getById($session['user_id']);

                return true;
            }
        }

        return false;
    }

    /**
     * Get user object
     *
     * @access public
     * @return DatabaseUserProvider
     */
    public function getUser()
    {
        if (empty($this->userInfo)) {
            return null;
        }

        return new DatabaseUserProvider($this->userInfo);
    }
}
