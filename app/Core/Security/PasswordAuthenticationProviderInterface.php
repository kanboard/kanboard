<?php

namespace Kanboard\Core\Security;

/**
 * Password Authentication Provider Interface
 *
 * @package  security
 * @author   Frederic Guillot
 */
interface PasswordAuthenticationProviderInterface extends AuthenticationProviderInterface
{
    /**
     * Get user object
     *
     * @access public
     * @return \Kanboard\Core\User\UserProviderInterface
     */
    public function getUser();

    /**
     * Set username
     *
     * @access public
     * @param  string $username
     */
    public function setUsername($username);

    /**
     * Set password
     *
     * @access public
     * @param  string $password
     */
    public function setPassword($password);
}
