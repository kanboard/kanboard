<?php

namespace Kanboard\Core\Security;

/**
 * Optional Authentication Provider Interface
 *
 * @package  security
 * @author   Frederic Guillot
 */
interface OptionalAuthenticationProviderInterface
{
    /**
     * Check if the authentication provider should be used
     *
     * @access public
     * @return boolean
     */
    public function isEnabled();
}
