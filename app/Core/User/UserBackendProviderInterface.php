<?php

namespace Kanboard\Core\User;

/**
 * User Backend Provider Interface
 *
 * @package  Kanboard\Core\User
 * @author   Frederic Guillot
 */
interface UserBackendProviderInterface
{
    /**
     * Find a user from a search query
     *
     * @access public
     * @param  string $input
     * @return UserProviderInterface[]
     */
    public function find($input);
}
