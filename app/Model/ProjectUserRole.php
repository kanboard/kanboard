<?php

namespace Kanboard\Model;

use Kanboard\Core\Security\Role;

/**
 * Project User Role
 *
 * @package  model
 * @author   Frederic Guillot
 */
class ProjectUserRole extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'project_has_users';

    /**
     * Get the list of active project for the given user
     *
     * @access public
     * @param  integer  $user_id
     * @return array
     */
    public function getActiveProjectsByUser($user_id)
    {
        return $this->getProjectsByUser($user_id, array(Project::ACTIVE));
    }

    /**
     * Get the list of project visible for the given user
     *
     * @access public
     * @param  integer  $user_id
     * @param  array    $status
     * @return array
     */
    public function getProjectsByUser($user_id, $status = array(Project::ACTIVE, Project::INACTIVE))
    {
        $userProjects = $this->db
            ->hashtable(Project::TABLE)
            ->beginOr()
            ->eq(self::TABLE.'.user_id', $user_id)
            ->eq(Project::TABLE.'.is_everybody_allowed', 1)
            ->closeOr()
            ->in(Project::TABLE.'.is_active', $status)
            ->join(self::TABLE, 'project_id', 'id')
            ->getAll(Project::TABLE.'.id', Project::TABLE.'.name');

        $groupProjects = $this->projectGroupRole->getProjectsByUser($user_id, $status);
        $projects = $userProjects + $groupProjects;

        asort($projects);

        return $projects;
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
        if ($this->projectPermission->isEverybodyAllowed($project_id)) {
            return Role::PROJECT_MEMBER;
        }

        $role = $this->db->table(self::TABLE)->eq('user_id', $user_id)->eq('project_id', $project_id)->findOneColumn('role');

        if (empty($role)) {
            $role = $this->projectGroupRole->getUserRole($project_id, $user_id);
        }

        return $role;
    }

    /**
     * Get all users associated directly to the project
     *
     * @access public
     * @param  integer $project_id
     * @return array
     */
    public function getUsers($project_id)
    {
        return $this->db->table(self::TABLE)
            ->columns(User::TABLE.'.id', User::TABLE.'.username', User::TABLE.'.name', self::TABLE.'.role')
            ->join(User::TABLE, 'id', 'user_id')
            ->eq('project_id', $project_id)
            ->asc(User::TABLE.'.username')
            ->asc(User::TABLE.'.name')
            ->findAll();
    }

    /**
     * Get all users (fetch users from groups)
     *
     * @access public
     * @param  integer $project_id
     * @return array
     */
    public function getAllUsers($project_id)
    {
        $userMembers = $this->getUsers($project_id);
        $groupMembers = $this->projectGroupRole->getUsers($project_id);
        $members = array_merge($userMembers, $groupMembers);

        return $this->user->prepareList($members);
    }

    /**
     * Get users grouped by role
     *
     * @access public
     * @param  integer   $project_id   Project id
     * @return array
     */
    public function getAllUsersGroupedByRole($project_id)
    {
        $users = array();

        $userMembers = $this->getUsers($project_id);
        $groupMembers = $this->projectGroupRole->getUsers($project_id);
        $members = array_merge($userMembers, $groupMembers);

        foreach ($members as $user) {
            if (! isset($users[$user['role']])) {
                $users[$user['role']] = array();
            }

            $users[$user['role']][$user['id']] = $user['name'] ?: $user['username'];
        }

        return $users;
    }

    /**
     * Get list of users that can be assigned to a task (only Manager and Member)
     *
     * @access public
     * @param  integer $project_id
     * @return array
     */
    public function getAssignableUsers($project_id)
    {
        if ($this->projectPermission->isEverybodyAllowed($project_id)) {
            return $this->user->getList();
        }

        $userMembers = $this->db->table(self::TABLE)
            ->columns(User::TABLE.'.id', User::TABLE.'.username', User::TABLE.'.name')
            ->join(User::TABLE, 'id', 'user_id')
            ->eq('project_id', $project_id)
            ->in(self::TABLE.'.role', array(Role::PROJECT_MANAGER, Role::PROJECT_MEMBER))
            ->findAll();

        $groupMembers = $this->projectGroupRole->getAssignableUsers($project_id);
        $members = array_merge($userMembers, $groupMembers);

        return $this->user->prepareList($members);
    }

    /**
     * Get list of users that can be assigned to a task (only Manager and Member)
     *
     * @access public
     * @param  integer   $project_id    Project id
     * @param  bool      $unassigned    Prepend the 'Unassigned' value
     * @param  bool      $everybody     Prepend the 'Everbody' value
     * @param  bool      $singleUser    If there is only one user return only this user
     * @return array
     */
    public function getAssignableUsersList($project_id, $unassigned = true, $everybody = false, $singleUser = false)
    {
        $users = $this->getAssignableUsers($project_id);

        if ($singleUser && count($users) === 1) {
            return $users;
        }

        if ($unassigned) {
            $users = array(t('Unassigned')) + $users;
        }

        if ($everybody) {
            $users = array(User::EVERYBODY_ID => t('Everybody')) + $users;
        }

        return $users;
    }

    /**
     * Add a user to the project
     *
     * @access public
     * @param  integer $project_id
     * @param  integer $user_id
     * @param  string  $role
     * @return boolean
     */
    public function addUser($project_id, $user_id, $role)
    {
        return $this->db->table(self::TABLE)->insert(array(
            'user_id' => $user_id,
            'project_id' => $project_id,
            'role' => $role,
        ));
    }

    /**
     * Remove a user from the project
     *
     * @access public
     * @param  integer $project_id
     * @param  integer $user_id
     * @return boolean
     */
    public function removeUser($project_id, $user_id)
    {
        return $this->db->table(self::TABLE)->eq('user_id', $user_id)->eq('project_id', $project_id)->remove();
    }

    /**
     * Change a user role for the project
     *
     * @access public
     * @param  integer $project_id
     * @param  integer $user_id
     * @param  string  $role
     * @return boolean
     */
    public function changeUserRole($project_id, $user_id, $role)
    {
        return $this->db->table(self::TABLE)
            ->eq('user_id', $user_id)
            ->eq('project_id', $project_id)
            ->update(array(
                'role' => $role,
            ));
    }

    /**
     * Copy user access from a project to another one
     *
     * @param  integer   $project_src_id  Project Template
     * @return integer   $project_dst_id  Project that receives the copy
     * @return boolean
     */
    public function duplicate($project_src_id, $project_dst_id)
    {
        $rows = $this->db->table(self::TABLE)->eq('project_id', $project_src_id)->findAll();

        foreach ($rows as $row) {
            $result = $this->db->table(self::TABLE)->save(array(
                'project_id' => $project_dst_id,
                'user_id' => $row['user_id'],
                'role' => $row['role'],
            ));

            if (! $result) {
                return false;
            }
        }

        return true;
    }
}
