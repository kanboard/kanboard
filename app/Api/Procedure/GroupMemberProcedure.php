<?php

namespace Kanboard\Api\Procedure;

/**
 * Group Member API controller
 *
 * @package  Kanboard\Api\Procedure
 * @author   Frederic Guillot
 */
class GroupMemberProcedure extends BaseProcedure
{
    public function getMemberGroups($user_id)
    {
        return $this->groupMemberModel->getGroups($user_id);
    }

    public function getGroupMembers($group_id)
    {
        return $this->groupMemberModel->getMembers($group_id);
    }

    public function addGroupMember($group_id, $user_id)
    {
        return $this->groupMemberModel->addUser($group_id, $user_id);
    }

    public function removeGroupMember($group_id, $user_id)
    {
        return $this->groupMemberModel->removeUser($group_id, $user_id);
    }

    public function isGroupMember($group_id, $user_id)
    {
        return $this->groupMemberModel->isMember($group_id, $user_id);
    }
}
