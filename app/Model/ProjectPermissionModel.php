<?php

namespace Kanboard\Model;

use Kanboard\Core\Base;
use Kanboard\Core\Security\Role;
use Kanboard\Filter\ProjectGroupRoleProjectFilter;
use Kanboard\Filter\ProjectGroupRoleUsernameFilter;
use Kanboard\Filter\ProjectUserRoleProjectFilter;
use Kanboard\Filter\ProjectUserRoleUsernameFilter;

/**
 * Project Permission
 *
 * @package  Kanboard\Model
 * @author   Frederic Guillot
 */
class ProjectPermissionModel extends Base
{
    /**
     * Get query for project users overview
     *
     * @access public
     * @param  array    $project_ids
     * @param  string   $role
     * @return \PicoDb\Table
     */
    public function getQueryByRole(array $project_ids, $role)
    {
        if (empty($project_ids)) {
            $project_ids = array(-1);
        }

        return $this
            ->db
            ->table(ProjectUserRoleModel::TABLE)
            ->join(UserModel::TABLE, 'id', 'user_id')
            ->join(ProjectModel::TABLE, 'id', 'project_id')
            ->eq(ProjectUserRoleModel::TABLE.'.role', $role)
            ->eq(ProjectModel::TABLE.'.is_private', 0)
            ->in(ProjectModel::TABLE.'.id', $project_ids)
            ->columns(
                UserModel::TABLE.'.id',
                UserModel::TABLE.'.username',
                UserModel::TABLE.'.name',
                ProjectModel::TABLE.'.name AS project_name',
                ProjectModel::TABLE.'.id'
            );
    }

    /**
     * Get all usernames (fetch users from groups)
     *
     * @access public
     * @param  integer $project_id
     * @param  string  $input
     * @return array
     */
    public function findUsernames($project_id, $input)
    {
        $userMembers = $this->projectUserRoleQuery
            ->withFilter(new ProjectUserRoleProjectFilter($project_id))
            ->withFilter(new ProjectUserRoleUsernameFilter($input))
            ->getQuery()
            ->columns(
                UserModel::TABLE.'.id',
                UserModel::TABLE.'.username',
                UserModel::TABLE.'.name',
                UserModel::TABLE.'.email',
                UserModel::TABLE.'.avatar_path'
            )
            ->findAll();

        $groupMembers = $this->projectGroupRoleQuery
            ->withFilter(new ProjectGroupRoleProjectFilter($project_id))
            ->withFilter(new ProjectGroupRoleUsernameFilter($input))
            ->getQuery()
            ->columns(
                UserModel::TABLE.'.id',
                UserModel::TABLE.'.username',
                UserModel::TABLE.'.name',
                UserModel::TABLE.'.email',
                UserModel::TABLE.'.avatar_path'
            )
            ->findAll();

        $userMembers = array_column_index_unique($userMembers, 'username');
        $groupMembers = array_column_index_unique($groupMembers, 'username');
        $members = array_merge($userMembers, $groupMembers);

        ksort($members);

        return $members;
    }

    public function getMembers($project_id)
    {
        $userMembers = $this->projectUserRoleModel->getUsers($project_id);
        $groupMembers = $this->projectGroupRoleModel->getUsers($project_id);

        $userMembers = array_column_index_unique($userMembers, 'username');
        $groupMembers = array_column_index_unique($groupMembers, 'username');
        return array_merge($userMembers, $groupMembers);
    }

    public function getMembersWithEmail($project_id)
    {
        $members = $this->getMembers($project_id);
        return array_filter($members, function (array $user) {
            return ! empty($user['email']);
        });
    }

    /**
     * Return true if everybody is allowed for the project
     *
     * @access public
     * @param  integer   $project_id   Project id
     * @return bool
     */
    public function isEverybodyAllowed($project_id)
    {
        return $this->db
                    ->table(ProjectModel::TABLE)
                    ->eq('id', $project_id)
                    ->eq('is_everybody_allowed', 1)
                    ->exists();
    }

    /**
     * Return true if the user is allowed to access a project
     *
     * @param integer $project_id
     * @param integer $user_id
     * @return boolean
     */
    public function isUserAllowed($project_id, $user_id)
    {
        if ($this->userSession->isAdmin()) {
            return true;
        }

        return in_array(
            $this->projectUserRoleModel->getUserRole($project_id, $user_id),
            array(Role::PROJECT_MANAGER, Role::PROJECT_MEMBER, Role::PROJECT_VIEWER)
        );
    }

    /**
     * Return true if the user is assignable
     *
     * @access public
     * @param  integer  $project_id
     * @param  integer  $user_id
     * @return boolean
     */
    public function isAssignable($project_id, $user_id)
    {
        if ($this->userModel->isActive($user_id)) {
            $role = $this->projectUserRoleModel->getUserRole($project_id, $user_id);

            return ! empty($role) && $role !== Role::PROJECT_VIEWER;
        }

        return false;
    }

    /**
     * Return true if the user is member
     *
     * @access public
     * @param  integer  $project_id
     * @param  integer  $user_id
     * @return boolean
     */
    public function isMember($project_id, $user_id)
    {
        return in_array($this->projectUserRoleModel->getUserRole($project_id, $user_id), array(Role::PROJECT_MEMBER, Role::PROJECT_MANAGER, Role::PROJECT_VIEWER));
    }

    /**
     * Get active project ids by user
     *
     * @access public
     * @param  integer $user_id
     * @return array
     */
    public function getActiveProjectIds($user_id)
    {
        return array_keys($this->projectUserRoleModel->getActiveProjectsByUser($user_id));
    }

    /**
     * Get all project ids by user
     *
     * @access public
     * @param  integer $user_id
     * @return array
     */
    public function getProjectIds($user_id)
    {
        return array_keys($this->projectUserRoleModel->getProjectsByUser($user_id));
    }

    /**
     * Copy permissions to another project
     *
     * @param  integer  $project_src_id  Project Template
     * @param  integer  $project_dst_id  Project that receives the copy
     * @return boolean
     */
    public function duplicate($project_src_id, $project_dst_id)
    {
        return $this->projectUserRoleModel->duplicate($project_src_id, $project_dst_id) &&
            $this->projectGroupRoleModel->duplicate($project_src_id, $project_dst_id);
    }
}
