<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;
use Kanboard\Model\GroupMember;
use Kanboard\Model\ProjectGroupRole;
use Kanboard\Model\User;

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
            ->join(GroupMember::TABLE, 'group_id', 'group_id', ProjectGroupRole::TABLE)
            ->join(User::TABLE, 'id', 'user_id', GroupMember::TABLE)
            ->ilike(User::TABLE.'.username', $this->value.'%');

        return $this;
    }
}
