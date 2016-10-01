<?php

namespace Kanboard\Group;

use Kanboard\Core\Base;
use Kanboard\Core\Group\GroupBackendProviderInterface;

/**
 * Database Backend Group Provider
 *
 * @package  group
 * @author   Frederic Guillot
 */
class DatabaseBackendGroupProvider extends Base implements GroupBackendProviderInterface
{
    /**
     * Find a group from a search query
     *
     * @access public
     * @param  string $input
     * @return DatabaseGroupProvider[]
     */
    public function find($input)
    {
        $result = array();
        $groups = $this->groupModel->search($input);

        foreach ($groups as $group) {
            $result[] = new DatabaseGroupProvider($group);
        }

        return $result;
    }
}
