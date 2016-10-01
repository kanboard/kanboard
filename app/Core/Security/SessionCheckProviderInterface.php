<?php

namespace Kanboard\Core\Security;

/**
 * Session Check Provider Interface
 *
 * @package  security
 * @author   Frederic Guillot
 */
interface SessionCheckProviderInterface
{
    /**
     * Check if the user session is valid
     *
     * @access public
     * @return boolean
     */
    public function isValidSession();
}
