<?php

namespace Kanboard\Model;

use Kanboard\Core\Base;

/**
 * Class ProjectRoleModel
 *
 * @package Kanboard\Model
 * @author  Frederic Guillot
 */
class ProjectRoleModel extends Base
{
    const TABLE = 'project_has_roles';

    /**
     * Get all project roles
     *
     * @param  int $project_id
     * @return array
     */
    public function getAll($project_id)
    {
        return $this->db->table(self::TABLE)
            ->eq('project_id', $project_id)
            ->asc('role')
            ->findAll();
    }

    /**
     * Create a new project role
     *
     * @param  int $project_id
     * @param  string $role
     * @return bool|int
     */
    public function create($project_id, $role)
    {
        return $this->db
            ->table(self::TABLE)
            ->persist(array(
                'project_id' => $project_id,
                'role' => $role,
            ));
    }

    /**
     * Update a project role
     *
     * @param  int $role_id
     * @param  int $project_id
     * @param  string $role
     * @return bool
     */
    public function update($role_id, $project_id, $role)
    {
        return $this->db
            ->table(self::TABLE)
            ->eq('role_id', $role_id)
            ->eq('project_id', $project_id)
            ->update(array(
                'role' => $role,
            ));
    }

    /**
     * Remove a project role
     *
     * @param  int $project_id
     * @param  int $role_id
     * @return bool
     */
    public function remove($project_id, $role_id)
    {
        return $this->db
            ->table(self::TABLE)
            ->eq('project_id', $project_id)
            ->eq('role_id', $role_id)
            ->remove();
    }
}
