<?php

namespace Kanboard\Model;

use Kanboard\Core\Base;

/**
 * Class ColumnMoveRestrictionModel
 *
 * @package Kanboard\Model
 * @author  Frederic Guillot
 */
class ColumnMoveRestrictionModel extends Base
{
    const TABLE = 'column_has_move_restrictions';

    /**
     * Check if the custom project role is allowed to move a task
     *
     * @param  int $project_id
     * @param  string $role
     * @param  int $src_column_id
     * @param  int $dst_column_id
     * @return int
     */
    public function isAllowed($project_id, $role, $src_column_id, $dst_column_id)
    {
        return ! $this->db->table(self::TABLE)
            ->left(ProjectRoleModel::TABLE, 'pr', 'role_id', self::TABLE, 'role_id')
            ->eq(self::TABLE.'.project_id', $project_id)
            ->eq(self::TABLE.'.src_column_id', $src_column_id)
            ->eq(self::TABLE.'.dst_column_id', $dst_column_id)
            ->eq('pr.role', $role)
            ->exists();
    }

    /**
     * Get all project column restrictions
     *
     * @param  int $project_id
     * @return array
     */
    public function getAll($project_id)
    {
        return $this->db->table(self::TABLE)
            ->columns(
                'restriction_id',
                'src_column_id',
                'dst_column_id',
                'pr.role',
                'sc.title as src_column_title',
                'dc.title as dst_column_title'
            )
            ->left(ColumnModel::TABLE, 'sc', 'id', self::TABLE, 'src_column_id')
            ->left(ColumnModel::TABLE, 'dc', 'id', self::TABLE, 'dst_column_id')
            ->left(ProjectRoleModel::TABLE, 'pr', 'role_id', self::TABLE, 'role_id')
            ->eq(self::TABLE.'.project_id', $project_id)
            ->findAll();
    }

    /**
     * Create a new column restriction
     *
     * @param  int    $project_id
     * @param  int    $role_id
     * @param  int    $src_column_id
     * @param  int    $dst_column_id
     * @return bool|int
     */
    public function create($project_id, $role_id, $src_column_id, $dst_column_id)
    {
        return $this->db
            ->table(self::TABLE)
            ->persist(array(
                'project_id' => $project_id,
                'role_id' => $role_id,
                'src_column_id' => $src_column_id,
                'dst_column_id' => $dst_column_id,
            ));
    }

    /**
     * Remove a permission
     *
     * @param  int $restriction_id
     * @return bool
     */
    public function remove($restriction_id)
    {
        return $this->db->table(self::TABLE)->eq('restriction_id', $restriction_id)->remove();
    }
}
