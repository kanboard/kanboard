<?php

namespace Kanboard\Auth;

use Kanboard\Core\Base;
use Kanboard\Core\Security\PreAuthenticationProviderInterface;
use Kanboard\Core\Security\SessionCheckProviderInterface;
use Kanboard\User\ReverseProxyUserProvider;

/**
 * ReverseProxy Authentication Provider
 *
 * @package  auth
 * @author   Frederic Guillot
 */
class ReverseProxyAuth extends Base implements PreAuthenticationProviderInterface, SessionCheckProviderInterface
{
    /**
     * User properties
     *
     * @access private
     * @var \Kanboard\User\ReverseProxyUserProvider
     */
    private $user = null;

    /**
     * Get authentication provider name
     *
     * @access public
     * @return string
     */
    public function getName()
    {
        return 'ReverseProxy';
    }

    /**
     * Authenticate the user
     *
     * @access public
     * @return boolean
     */
    public function authenticate()
    {
        $username = $this->request->getRemoteUser();

        if (! empty($username)) {
            $this->user = new ReverseProxyUserProvider($username);
            return true;
        }

        return false;
    }

    /**
     * Check if the user session is valid
     *
     * @access public
     * @return boolean
     */
    public function isValidSession()
    {
        return $this->request->getRemoteUser() === $this->userSession->getUsername();
    }

    /**
     * Get user object
     *
     * @access public
     * @return ReverseProxyUserProvider
     */
    public function getUser()
    {
        return $this->user;
    }
}
