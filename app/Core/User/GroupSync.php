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
     * @param  integer $userId
     * @param  array   $externalGroupIds
     */
    public function synchronize($userId, array $externalGroupIds)
    {
        $userGroups = $this->groupMemberModel->getGroups($userId);
        $this->addGroups($userId, $userGroups, $externalGroupIds);
        $this->removeGroups($userId, $userGroups, $externalGroupIds);
    }

    /**
     * Add missing groups to the user
     *
     * @access protected
     * @param integer $userId
     * @param array   $userGroups
     * @param array   $externalGroupIds
     */
    protected function addGroups($userId, array $userGroups, array $externalGroupIds)
    {
        $userGroupIds = array_column($userGroups, 'external_id', 'external_id');

        foreach ($externalGroupIds as $externalGroupId) {
            if (! isset($userGroupIds[$externalGroupId])) {
                $group = $this->groupModel->getByExternalId($externalGroupId);

                if (! empty($group)) {
                    $this->groupMemberModel->addUser($group['id'], $userId);
                }
            }
        }
    }

    /**
     * Remove groups from the user
     *
     * @access protected
     * @param integer $userId
     * @param array   $userGroups
     * @param array   $externalGroupIds
     */
    protected function removeGroups($userId, array $userGroups, array $externalGroupIds)
    {
        foreach ($userGroups as $userGroup) {
            if (! empty($userGroup['external_id']) && ! in_array($userGroup['external_id'], $externalGroupIds)) {
                $this->groupMemberModel->removeUser($userGroup['id'], $userId);
            }
        }
    }
}
