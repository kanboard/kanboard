<?php

namespace Kanboard\Api;

use Kanboard\Core\Security\Role;

/**
 * Project Permission API controller
 *
 * @package  api
 * @author   Frederic Guillot
 */
class ProjectPermission extends \Kanboard\Core\Base
{
    public function getProjectUsers($project_id)
    {
        return $this->projectUserRole->getAllUsers($project_id);
    }

    public function getAssignableUsers($project_id, $prepend_unassigned = false)
    {
        return $this->projectUserRole->getAssignableUsersList($project_id, $prepend_unassigned);
    }

    public function addProjectUser($project_id, $user_id, $role = Role::PROJECT_MEMBER)
    {
        return $this->projectUserRole->addUser($project_id, $user_id, $role);
    }

    public function addProjectGroup($project_id, $group_id, $role = Role::PROJECT_MEMBER)
    {
        return $this->projectGroupRole->addGroup($project_id, $group_id, $role);
    }

    public function removeProjectUser($project_id, $user_id)
    {
        return $this->projectUserRole->removeUser($project_id, $user_id);
    }

    public function removeProjectGroup($project_id, $group_id)
    {
        return $this->projectGroupRole->removeGroup($project_id, $group_id);
    }

    public function changeProjectUserRole($project_id, $user_id, $role)
    {
        return $this->projectUserRole->changeUserRole($project_id, $user_id, $role);
    }

    public function changeProjectGroupRole($project_id, $group_id, $role)
    {
        return $this->projectGroupRole->changeGroupRole($project_id, $group_id, $role);
    }

    // Deprecated
    public function getMembers($project_id)
    {
        return $this->getProjectUsers($project_id);
    }

    // Deprecated
    public function revokeUser($project_id, $user_id)
    {
        return $this->removeProjectUser($project_id, $user_id);
    }

    // Deprecated
    public function allowUser($project_id, $user_id)
    {
        return $this->addProjectUser($project_id, $user_id);
    }
}
