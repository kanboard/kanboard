<?php

namespace Kanboard\Api;

/**
 * Group Member API controller
 *
 * @package  api
 * @author   Frederic Guillot
 */
class GroupMember extends \Kanboard\Core\Base
{
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
