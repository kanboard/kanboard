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

    protected $ruleMapping = array(
        self::RULE_TASK_CREATION => array(
            array('controller' => 'TaskCreationController', 'method' => '*'),
        )
    );

    /**
     * Get rules
     *
     * @return array
     */
    public function getRules()
    {
        return array(
            self::RULE_TASK_CREATION => t('Task creation is not permitted'),
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
                self::TABLE.'.restriction_id',
                self::TABLE.'.project_id',
                self::TABLE.'.role_id',
                self::TABLE.'.rule'
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
        $rules = $this->db
            ->table(self::TABLE)
            ->columns(
                self::TABLE.'.restriction_id',
                self::TABLE.'.project_id',
                self::TABLE.'.role_id',
                self::TABLE.'.rule',
                'pr.role'
            )
            ->eq(self::TABLE.'.project_id', $project_id)
            ->eq('role', $role)
            ->left(ProjectRoleModel::TABLE, 'pr', 'role_id', self::TABLE, 'role_id')
            ->findAll();

        foreach ($rules as &$rule) {
            $rule['acl'] = $this->ruleMapping[$rule['rule']];
        }

        return $rules;
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
     * Check if the controller/method is allowed
     *
     * @param  array  $restrictions
     * @param  string $controller
     * @param  string $method
     * @return bool
     */
    public function isAllowed(array $restrictions, $controller, $method)
    {
        $controller = strtolower($controller);
        $method = strtolower($method);

        foreach ($restrictions as $restriction) {
            foreach ($restriction['acl'] as $acl) {
                $acl['controller'] = strtolower($acl['controller']);
                $acl['method'] = strtolower($acl['method']);

                if ($acl['controller'] === $controller && ($acl['method'] === '*' || $acl['method'] === $method)) {
                    return false;
                }
            }
        }

        return true;
    }
}
