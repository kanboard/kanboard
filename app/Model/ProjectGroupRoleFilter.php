<?php

namespace Kanboard\Model;

/**
 * Project Group Role Filter
 *
 * @package  model
 * @author   Frederic Guillot
 */
class ProjectGroupRoleFilter extends Base
{
    /**
     * Query
     *
     * @access protected
     * @var \PicoDb\Table
     */
    protected $query;

    /**
     * Initialize filter
     *
     * @access  public
     * @return  UserFilter
     */
    public function create()
    {
        $this->query = $this->db->table(ProjectGroupRole::TABLE);
        return $this;
    }

    /**
     * Get all results of the filter
     *
     * @access public
     * @param  string $column
     * @return array
     */
    public function findAll($column = '')
    {
        if ($column !== '') {
            return $this->query->asc($column)->findAllByColumn($column);
        }

        return $this->query->findAll();
    }

    /**
     * Get the PicoDb query
     *
     * @access public
     * @return \PicoDb\Table
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Filter by project id
     *
     * @access public
     * @param  integer $project_id
     * @return ProjectUserRoleFilter
     */
    public function filterByProjectId($project_id)
    {
        $this->query->eq(ProjectGroupRole::TABLE.'.project_id', $project_id);
        return $this;
    }

    /**
     * Filter by username
     *
     * @access public
     * @param  string $input
     * @return ProjectUserRoleFilter
     */
    public function startWithUsername($input)
    {
        $this->query
            ->join(GroupMember::TABLE, 'group_id', 'group_id', ProjectGroupRole::TABLE)
            ->join(User::TABLE, 'id', 'user_id', GroupMember::TABLE)
            ->ilike(User::TABLE.'.username', $input.'%');

        return $this;
    }
}
