<?php

namespace Kanboard\Core\Security;

/**
 * Authentication Provider Interface
 *
 * @package  security
 * @author   Frederic Guillot
 */
interface AuthenticationProviderInterface
{
    /**
     * Get authentication provider name
     *
     * @access public
     * @return string
     */
    public function getName();

    /**
     * Authenticate the user
     *
     * @access public
     * @return boolean
     */
    public function authenticate();
}
