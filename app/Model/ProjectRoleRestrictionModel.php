<?php

namespace Kanboard\Model;

use Kanboard\Core\Base;

/**
 * Class ProjectRoleRestrictionModel
 *
 * @package Kanboard\Model
 * @author  Frederic Guillot
 */
class ProjectRoleRestrictionModel extends Base
{
    const TABLE = 'project_role_has_restrictions';

    const RULE_TASK_CREATION = 'task_creation';
    const RULE_TASK_SUPPRESSION = 'task_remove';
    const RULE_TASK_OPEN_CLOSE = 'task_open_close';
    const RULE_TASK_MOVE = 'task_move';
    const RULE_TASK_CHANGE_ASSIGNEE = 'task_change_assignee';
    const RULE_TASK_UPDATE_ASSIGNED = 'task_update_assigned';

    /**
     * Get rules
     *
     * @return array
     */
    public function getRules()
    {
        return array(
            self::RULE_TASK_CREATION        => t('Task creation is not permitted'),
            self::RULE_TASK_SUPPRESSION     => t('Task suppression is not permitted'),
            self::RULE_TASK_OPEN_CLOSE      => t('Closing or opening a task is not permitted'),
            self::RULE_TASK_MOVE            => t('Moving a task is not permitted'),
            self::RULE_TASK_CHANGE_ASSIGNEE => t('Changing assignee is not permitted'),
            self::RULE_TASK_UPDATE_ASSIGNED => t('Update only assigned tasks is permitted'),
        );
    }

    /**
     * Get a single restriction
     *
     * @param  integer $project_id
     * @param  integer $restriction_id
     * @return array|null
     */
    public function getById($project_id, $restriction_id)
    {
        return $this->db
            ->table(self::TABLE)
            ->eq('project_id', $project_id)
            ->eq('restriction_id', $restriction_id)
            ->findOne();
    }

    /**
     * Get restrictions
     *
     * @param  int    $project_id
     * @return array
     */
    public function getAll($project_id)
    {
        $rules = $this->getRules();
        $restrictions = $this->db
            ->table(self::TABLE)
            ->columns(
                'restriction_id',
                'project_id',
                'role_id',
                'rule'
            )
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
                'restriction_id',
                'project_id',
                'role_id',
                'rule',
                'pr.role'
            )
            ->eq(self::TABLE.'.project_id', $project_id)
            ->eq('role', $role)
            ->left(ProjectRoleModel::TABLE, 'pr', 'role_id', self::TABLE, 'role_id')
            ->findAll();
    }

    /**
     * Create a new restriction
     *
     * @param  int $project_id
     * @param  int $role_id
     * @param  string $rule
     * @return bool|int
     */
    public function create($project_id, $role_id, $rule)
    {
        return $this->db->table(self::TABLE)
            ->persist(array(
                'project_id' => $project_id,
                'role_id' => $role_id,
                'rule' => $rule,
            ));
    }

    /**
     * Remove a restriction
     *
     * @param  integer $restriction_id
     * @return bool
     */
    public function remove($restriction_id)
    {
        return $this->db->table(self::TABLE)->eq('restriction_id', $restriction_id)->remove();
    }

    /**
     * Copy role restriction models from a custome_role in the src project to the dst custom_role of the dst project 
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
            $result = $this->db->table(self::TABLE)->persist(array(
                'project_id' => $project_dst_id,
                'role_id' => $role_dst_id,
                'rule' => $row['rule'],
            ));
            
            if (! $result) {
                return false;
            }
        }
            
        return true;
    }
}
