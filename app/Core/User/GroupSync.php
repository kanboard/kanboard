<?php

namespace Kanboard\Core\User;

use Kanboard\Core\Base;

/**
 * Group Synchronization
 *
 * @package  user
 * @author   Frederic Guillot
 */
class GroupSync extends Base
{
    /**
     * Synchronize group membership
     *
     * @access public
     * @param  integer  $userId
     * @param  array    $groupIds
     */
    public function synchronize($userId, array $groupIds)
    {
        foreach ($groupIds as $groupId) {
            $group = $this->group->getByExternalId($groupId);

            if (! empty($group) && ! $this->groupMember->isMember($group['id'], $userId)) {
                $this->groupMember->addUser($group['id'], $userId);
            }
        }
    }
}
