<?php

namespace Kanboard\Api;

use Kanboard\Core\Base;
use Kanboard\Core\Security\Role;

/**
 * Project Permission API controller
 *
 * @package  Kanboard\Api
 * @author   Frederic Guillot
 */
class ProjectPermissionApi extends Base
{
    public function getProjectUsers($project_id)
    {
        return $this->projectUserRoleModel->getAllUsers($project_id);
    }

    public function getAssignableUsers($project_id, $prepend_unassigned = false)
    {
        return $this->projectUserRoleModel->getAssignableUsersList($project_id, $prepend_unassigned);
    }

    public function addProjectUser($project_id, $user_id, $role = Role::PROJECT_MEMBER)
    {
        return $this->projectUserRoleModel->addUser($project_id, $user_id, $role);
    }

    public function addProjectGroup($project_id, $group_id, $role = Role::PROJECT_MEMBER)
    {
        return $this->projectGroupRoleModel->addGroup($project_id, $group_id, $role);
    }

    public function removeProjectUser($project_id, $user_id)
    {
        return $this->projectUserRoleModel->removeUser($project_id, $user_id);
    }

    public function removeProjectGroup($project_id, $group_id)
    {
        return $this->projectGroupRoleModel->removeGroup($project_id, $group_id);
    }

    public function changeProjectUserRole($project_id, $user_id, $role)
    {
        return $this->projectUserRoleModel->changeUserRole($project_id, $user_id, $role);
    }

    public function changeProjectGroupRole($project_id, $group_id, $role)
    {
        return $this->projectGroupRoleModel->changeGroupRole($project_id, $group_id, $role);
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
