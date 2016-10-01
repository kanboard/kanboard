<?php

namespace Kanboard\Core\Group;

/**
 * Group Manager
 *
 * @package  group
 * @author   Frederic Guillot
 */
class GroupManager
{
    /**
     * List of backend providers
     *
     * @access private
     * @var array
     */
    private $providers = array();

    /**
     * Register a new group backend provider
     *
     * @access public
     * @param  GroupBackendProviderInterface $provider
     * @return GroupManager
     */
    public function register(GroupBackendProviderInterface $provider)
    {
        $this->providers[] = $provider;
        return $this;
    }

    /**
     * Find a group from a search query
     *
     * @access public
     * @param  string $input
     * @return GroupProviderInterface[]
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
     * Remove duplicated groups
     *
     * @access private
     * @param  array $groups
     * @return GroupProviderInterface[]
     */
    private function removeDuplicates(array $groups)
    {
        $result = array();

        foreach ($groups as $group) {
            if (! isset($result[$group->getName()])) {
                $result[$group->getName()] = $group;
            }
        }

        return array_values($result);
    }
}
