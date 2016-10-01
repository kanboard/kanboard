<?php

namespace Kanboard\Core\Group;

/**
 * Group Backend Provider Interface
 *
 * @package  group
 * @author   Frederic Guillot
 */
interface GroupBackendProviderInterface
{
    /**
     * Find a group from a search query
     *
     * @access public
     * @param  string $input
     * @return GroupProviderInterface[]
     */
    public function find($input);
}
