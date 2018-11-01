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
     * Fetch one restriction
     *
     * @param  int $project_id
     * @param  int $restriction_id
     * @return array|null
     */
    public function getById($project_id, $restriction_id)
    {
        return $this->db
            ->table(self::TABLE)
            ->columns(
                self::TABLE.'.restriction_id',
                self::TABLE.'.project_id',
                self::TABLE.'.role_id',
                self::TABLE.'.src_column_id',
                self::TABLE.'.dst_column_id',
                self::TABLE.'.only_assigned',
                'pr.role',
                'sc.title as src_column_title',
                'dc.title as dst_column_title'
            )
            ->left(ColumnModel::TABLE, 'sc', 'id', self::TABLE, 'src_column_id')
            ->left(ColumnModel::TABLE, 'dc', 'id', self::TABLE, 'dst_column_id')
            ->left(ProjectRoleModel::TABLE, 'pr', 'role_id', self::TABLE, 'role_id')
            ->eq(self::TABLE.'.project_id', $project_id)
            ->eq(self::TABLE.'.restriction_id', $restriction_id)
            ->findOne();
    }

    /**
     * Get all project column restrictions
     *
     * @param  int $project_id
     * @return array
     */
    public function getAll($project_id)
    {
        return $this->db
            ->table(self::TABLE)
            ->columns(
                self::TABLE.'.restriction_id',
                self::TABLE.'.project_id',
                self::TABLE.'.role_id',
                self::TABLE.'.src_column_id',
                self::TABLE.'.dst_column_id',
                self::TABLE.'.only_assigned',
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
     * Get all sortable column Ids
     *
     * @param  int    $project_id
     * @param  string $role
     * @return array
     */
    public function getSortableColumns($project_id, $role)
    {
        return $this->db
            ->table(self::TABLE)
            ->columns(self::TABLE.'.src_column_id', self::TABLE.'.dst_column_id', self::TABLE.'.only_assigned')
            ->left(ProjectRoleModel::TABLE, 'pr', 'role_id', self::TABLE, 'role_id')
            ->eq(self::TABLE.'.project_id', $project_id)
            ->eq('pr.role', $role)
            ->findAll();
    }

    /**
     * Create a new column restriction
     *
     * @param  int    $project_id
     * @param  int    $role_id
     * @param  int    $src_column_id
     * @param  int    $dst_column_id
     * @param  bool   $only_assigned
     * @return bool|int
     */
    public function create($project_id, $role_id, $src_column_id, $dst_column_id, $only_assigned = false)
    {
        return $this->db
            ->table(self::TABLE)
            ->persist(array(
                'project_id' => $project_id,
                'role_id' => $role_id,
                'src_column_id' => $src_column_id,
                'dst_column_id' => $dst_column_id,
                'only_assigned' => (int) $only_assigned,
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

    /**
     * Copy column_move_restriction models from a custome_role in the src project to the dst custom_role of the dst project 
     *
     * @param  integer $project_src_id
     * @param  integer $project_dst_id
     * @param  integer $role_src_id
     * @param  integer $role_dst_id
     * @return boolean
     */
    public function duplicate($project_src_id, $project_dst_id, $role_src_id, $role_dst_id)
    {
        $rows = $this->db->table(self::TABLE)
            ->eq('project_id', $project_src_id)
            ->eq('role_id', $role_src_id)
            ->findAll();

        foreach ($rows as $row) {
            $src_column_title = $this->columnModel->getColumnTitleById($row['src_column_id']);
            $dst_column_title = $this->columnModel->getColumnTitleById($row['dst_column_id']);
            $src_column_id = $this->columnModel->getColumnIdByTitle($project_dst_id, $src_column_title);
            $dst_column_id = $this->columnModel->getColumnIdByTitle($project_dst_id, $dst_column_title);

            if (! $dst_column_id) {
                $this->logger->error("The column $dst_column_title is not present in project $project_dst_id");
                return false;
            }

            if (! $src_column_id) {
                $this->logger->error("The column $src_column_title is not present in project $project_dst_id");
                return false;
            }

            $result = $this->db->table(self::TABLE)->persist(array(
                'project_id' => $project_dst_id,
                'role_id' => $role_dst_id,
                'src_column_id' => $src_column_id,
                'dst_column_id' => $dst_column_id,
                'only_assigned' => (int) $row['only_assigned'],
            ));
            
            if (! $result) {
                return false;
            }
        }
            
        return true;
    }
}
