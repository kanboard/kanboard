<?php

namespace Kanboard\Core\User;

/**
 * User Manager
 *
 * @package  Kanboard\Core\User
 * @author   Frederic Guillot
 */
class UserManager
{
    /**
     * List of backend providers
     *
     * @access protected
     * @var array
     */
    protected $providers = array();

    /**
     * Register a new group backend provider
     *
     * @access public
     * @param  UserBackendProviderInterface $provider
     * @return $this
     */
    public function register(UserBackendProviderInterface $provider)
    {
        $this->providers[] = $provider;
        return $this;
    }

    /**
     * Find a group from a search query
     *
     * @access public
     * @param  string $input
     * @return UserProviderInterface[]
     */
    public function find($input)
    {
        $groups = array();

        foreach ($this->providers as $provider) {
            $groups = array_merge($groups, $provider->find($input));
        }

        return $this->removeDuplicates($groups);
    }

    /**
     * Remove duplicated users
     *
     * @access protected
     * @param  array $users
     * @return UserProviderInterface[]
     */
    protected function removeDuplicates(array $users)
    {
        $result = array();

        foreach ($users as $user) {
            if (! isset($result[$user->getUsername()])) {
                $result[$user->getUsername()] = $user;
            }
        }

        return array_values($result);
    }
}
