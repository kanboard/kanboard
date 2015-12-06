<?php

namespace Kanboard\Model;

use Kanboard\Core\Security\Role;

/**
 * Project Permission
 *
 * @package  model
 * @author   Frederic Guillot
 */
class ProjectPermission extends Base
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
            ->table(self::TABLE)
            ->join(User::TABLE, 'id', 'user_id')
            ->join(Project::TABLE, 'id', 'project_id')
            ->eq(self::TABLE.'.role', $role)
            ->eq(Project::TABLE.'.is_private', 0)
            ->in(Project::TABLE.'.id', $project_ids)
            ->columns(
                User::TABLE.'.id',
                User::TABLE.'.username',
                User::TABLE.'.name',
                Project::TABLE.'.name AS project_name',
                Project::TABLE.'.id'
            );
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
                    ->table(Project::TABLE)
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
            $this->projectUserRole->getUserRole($project_id, $user_id),
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
    public function isMember($project_id, $user_id)
    {
        return in_array($this->projectUserRole->getUSerRole($project_id, $user_id), array(Role::PROJECT_MEMBER, Role::PROJECT_MANAGER));
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
        return array_keys($this->projectUserRole->getProjectsByUser($user_id, array(Project::ACTIVE)));
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
        return $this->projectUserRole->duplicate($project_src_id, $project_dst_id) &&
            $this->projectGroupRole->duplicate($project_src_id, $project_dst_id);
    }
}
