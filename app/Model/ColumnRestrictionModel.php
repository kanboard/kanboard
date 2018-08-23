<?php

namespace Kanboard\Model;

use Kanboard\Core\Base;

/**
 * Class ColumnRestrictionModel
 *
 * @package Kanboard\Model
 * @author  Frederic Guillot
 */
class ColumnRestrictionModel extends Base
{
    const TABLE = 'column_has_restrictions';

    const RULE_ALLOW_TASK_CREATION    = 'allow.task_creation';
    const RULE_ALLOW_TASK_OPEN_CLOSE  = 'allow.task_open_close';
    const RULE_BLOCK_TASK_CREATION    = 'block.task_creation';
    const RULE_BLOCK_TASK_OPEN_CLOSE  = 'block.task_open_close';

    /**
     * Get rules
     *
     * @return array
     */
    public function getRules()
    {
        return array(
            self::RULE_ALLOW_TASK_CREATION    => t('Task creation is permitted for this column'),
            self::RULE_ALLOW_TASK_OPEN_CLOSE  => t('Closing or opening a task is permitted for this column'),
            self::RULE_BLOCK_TASK_CREATION    => t('Task creation is blocked for this column'),
            self::RULE_BLOCK_TASK_OPEN_CLOSE  => t('Closing or opening a task is blocked for this column'),
        );
    }

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
                self::TABLE.'.column_id',
                self::TABLE.'.rule',
                'pr.role',
                'c.title as column_title'
            )
            ->left(ColumnModel::TABLE, 'c', 'id', self::TABLE, 'column_id')
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
        $rules = $this->getRules();
        $restrictions = $this->db
            ->table(self::TABLE)
            ->columns(
                self::TABLE.'.restriction_id',
                self::TABLE.'.project_id',
                self::TABLE.'.role_id',
                self::TABLE.'.column_id',
                self::TABLE.'.rule',
                'pr.role',
                'c.title as column_title'
            )
            ->left(ColumnModel::TABLE, 'c', 'id', self::TABLE, 'column_id')
            ->left(ProjectRoleModel::TABLE, 'pr', 'role_id', self::TABLE, 'role_id')
            ->eq(self::TABLE.'.project_id', $project_id)
            ->findAll();

        foreach ($restrictions as &$restriction) {
            $restriction['title'] = $rules[$restriction['rule']];
        }

        return $restrictions;
    }

    /**
     * Get restrictions
     *
     * @param  int    $project_id
     * @param  string $role
     * @return array
     */
    public function getAllByRole($project_id, $role)
    {
        return $this->db
            ->table(self::TABLE)
            ->columns(
                self::TABLE.'.restriction_id',
                self::TABLE.'.project_id',
                self::TABLE.'.role_id',
                self::TABLE.'.column_id',
                self::TABLE.'.rule',
                'pr.role'
            )
            ->eq(self::TABLE.'.project_id', $project_id)
            ->eq('pr.role', $role)
            ->left(ProjectRoleModel::TABLE, 'pr', 'role_id', self::TABLE, 'role_id')
            ->findAll();
    }

    /**
     * Create a new column restriction
     *
     * @param  int    $project_id
     * @param  int    $role_id
     * @param  int    $column_id
     * @param  int    $rule
     * @return bool|int
     */
    public function create($project_id, $role_id, $column_id, $rule)
    {
        return $this->db
            ->table(self::TABLE)
            ->persist(array(
                'project_id' => $project_id,
                'role_id' => $role_id,
                'column_id' => $column_id,
                'rule' => $rule,
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
     * Copy column_restriction models from a custome_role in the src project to the dst custom_role of the dst project 
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
            $column_title = $this->columnModel->getColumnTitleById($row['column_id']);
            $dst_column_id = $this->columnModel->getColumnIdByTitle($project_dst_id, $column_title);

            if (! $dst_column_id) {
                $this->logger->error("The column $column_title is not present in project $project_dst_id");
                return false;
            }

            $result = $this->db->table(self::TABLE)->persist(array(
                'project_id' => $project_dst_id,
                'role_id' => $role_dst_id,
                'column_id' => $dst_column_id,
                'rule' => $row['rule'],
            ));
            
            if (! $result) {
                return false;
            }
        }
            
        return true;
    }
}
