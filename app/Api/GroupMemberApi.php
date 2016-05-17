<?php

namespace Kanboard\Api;

use Kanboard\Core\Base;

/**
 * Group Member API controller
 *
 * @package  Kanboard\Api
 * @author   Frederic Guillot
 */
class GroupMemberApi extends Base
{
    public function getMemberGroups($user_id)
    {
        return $this->groupMember->getGroups($user_id);
    }

    public function getGroupMembers($group_id)
    {
        return $this->groupMember->getMembers($group_id);
    }

    public function addGroupMember($group_id, $user_id)
    {
        return $this->groupMember->addUser($group_id, $user_id);
    }

    public function removeGroupMember($group_id, $user_id)
    {
        return $this->groupMember->removeUser($group_id, $user_id);
    }

    public function isGroupMember($group_id, $user_id)
    {
        return $this->groupMember->isMember($group_id, $user_id);
    }
}
