<?php

namespace Kanboard\Core\Group;

/**
 * Group Backend Provider Interface
 *
 * @package  Kanboard\Core\Group
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
