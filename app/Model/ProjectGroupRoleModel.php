<?php

namespace Kanboard\Model;

use Kanboard\Core\Base;
use Kanboard\Core\Security\Role;

/**
 * Project Group Role
 *
 * @package  Kanboard\Model
 * @author   Frederic Guillot
 */
class ProjectGroupRoleModel extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'project_has_groups';

    /**
     * Get the list of project visible by the given user according to groups
     *
     * @access public
     * @param  integer  $user_id
     * @param  array    $status
     * @return array
     */
    public function getProjectsByUser($user_id, $status = array(ProjectModel::ACTIVE, ProjectModel::INACTIVE))
    {
        return $this->db
            ->hashtable(ProjectModel::TABLE)
            ->join(self::TABLE, 'project_id', 'id')
            ->join(GroupMemberModel::TABLE, 'group_id', 'group_id', self::TABLE)
            ->eq(GroupMemberModel::TABLE.'.user_id', $user_id)
            ->in(ProjectModel::TABLE.'.is_active', $status)
            ->getAll(ProjectModel::TABLE.'.id', ProjectModel::TABLE.'.name');
    }

    /**
     * For a given project get the role of the specified user
     *
     * @access public
     * @param  integer  $project_id
     * @param  integer  $user_id
     * @return string
     */
    public function getUserRole($project_id, $user_id)
    {
        $roles = $this->db->table(self::TABLE)
            ->join(GroupMemberModel::TABLE, 'group_id', 'group_id', self::TABLE)
            ->eq(GroupMemberModel::TABLE.'.user_id', $user_id)
            ->eq(self::TABLE.'.project_id', $project_id)
            ->findAllByColumn('role');

        return $this->projectAccessMap->getHighestRole($roles);
    }

    /**
     * Get all groups associated directly to the project
     *
     * @access public
     * @param  integer $project_id
     * @return array
     */
    public function getGroups($project_id)
    {
        return $this->db->table(self::TABLE)
            ->columns(GroupModel::TABLE.'.id', GroupModel::TABLE.'.name', self::TABLE.'.role')
            ->join(GroupModel::TABLE, 'id', 'group_id')
            ->eq('project_id', $project_id)
            ->asc('name')
            ->findAll();
    }

    /**
     * From groups get all users associated to the project
     *
     * @access public
     * @param  integer $project_id
     * @return array
     */
    public function getUsers($project_id)
    {
        return $this->db->table(self::TABLE)
            ->columns(
                UserModel::TABLE.'.id',
                UserModel::TABLE.'.username',
                UserModel::TABLE.'.name',
                UserModel::TABLE.'.email',
                self::TABLE.'.role'
            )
            ->join(GroupMemberModel::TABLE, 'group_id', 'group_id', self::TABLE)
            ->join(UserModel::TABLE, 'id', 'user_id', GroupMemberModel::TABLE)
            ->eq(self::TABLE.'.project_id', $project_id)
            ->asc(UserModel::TABLE.'.username')
            ->findAll();
    }

    /**
     * From groups get all users assignable to tasks
     *
     * @access public
     * @param  integer $project_id
     * @return array
     */
    public function getAssignableUsers($project_id)
    {
        return $this->db->table(UserModel::TABLE)
            ->columns(UserModel::TABLE.'.id', UserModel::TABLE.'.username', UserModel::TABLE.'.name')
            ->join(GroupMemberModel::TABLE, 'user_id', 'id', UserModel::TABLE)
            ->join(self::TABLE, 'group_id', 'group_id', GroupMemberModel::TABLE)
            ->eq(self::TABLE.'.project_id', $project_id)
            ->eq(UserModel::TABLE.'.is_active', 1)
            ->in(self::TABLE.'.role', array(Role::PROJECT_MANAGER, Role::PROJECT_MEMBER))
            ->asc(UserModel::TABLE.'.username')
            ->findAll();
    }

    /**
     * Add a group to the project
     *
     * @access public
     * @param  integer $project_id
     * @param  integer $group_id
     * @param  string  $role
     * @return boolean
     */
    public function addGroup($project_id, $group_id, $role)
    {
        return $this->db->table(self::TABLE)->insert(array(
            'group_id' => $group_id,
            'project_id' => $project_id,
            'role' => $role,
        ));
    }

    /**
     * Remove a group from the project
     *
     * @access public
     * @param  integer $project_id
     * @param  integer $group_id
     * @return boolean
     */
    public function removeGroup($project_id, $group_id)
    {
        return $this->db->table(self::TABLE)->eq('group_id', $group_id)->eq('project_id', $project_id)->remove();
    }

    /**
     * Change a group role for the project
     *
     * @access public
     * @param  integer $project_id
     * @param  integer $group_id
     * @param  string  $role
     * @return boolean
     */
    public function changeGroupRole($project_id, $group_id, $role)
    {
        return $this->db->table(self::TABLE)
            ->eq('group_id', $group_id)
            ->eq('project_id', $project_id)
            ->update(array(
                'role' => $role,
            ));
    }

    /**
     * Copy group access from a project to another one
     *
     * @param  integer   $project_src_id  Project Template
     * @param  integer   $project_dst_id  Project that receives the copy
     * @return boolean
     */
    public function duplicate($project_src_id, $project_dst_id)
    {
        $rows = $this->db->table(self::TABLE)->eq('project_id', $project_src_id)->findAll();

        foreach ($rows as $row) {
            $result = $this->db->table(self::TABLE)->save(array(
                'project_id' => $project_dst_id,
                'group_id' => $row['group_id'],
                'role' => $row['role'],
            ));

            if (! $result) {
                return false;
            }
        }

        return true;
    }
}
