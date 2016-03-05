<?php

namespace Kanboard\Core\Security;

/**
 * Pre-Authentication Provider Interface
 *
 * @package  security
 * @author   Frederic Guillot
 */
interface PreAuthenticationProviderInterface extends AuthenticationProviderInterface
{
    /**
     * Get user object
     *
     * @access public
     * @return \Kanboard\Core\User\UserProviderInterface
     */
    public function getUser();
}
