<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;
use Kanboard\Model\GroupMemberModel;
use Kanboard\Model\ProjectGroupRoleModel;
use Kanboard\Model\UserModel;

/**
 * Filter ProjectGroupRole users by username
 *
 * @package filter
 * @author  Frederic Guillot
 */
class ProjectGroupRoleUsernameFilter extends BaseFilter implements FilterInterface
{
    /**
     * Get search attribute
     *
     * @access public
     * @return string[]
     */
    public function getAttributes()
    {
        return array();
    }

    /**
     * Apply filter
     *
     * @access public
     * @return FilterInterface
     */
    public function apply()
    {
        $this->query
            ->join(GroupMemberModel::TABLE, 'group_id', 'group_id', ProjectGroupRoleModel::TABLE)
            ->join(UserModel::TABLE, 'id', 'user_id', GroupMemberModel::TABLE)
            ->ilike(UserModel::TABLE.'.username', $this->value.'%');

        return $this;
    }
}
