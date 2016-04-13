<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;
use Kanboard\Model\User;

/**
 * Filter ProjectUserRole users by username
 *
 * @package filter
 * @author  Frederic Guillot
 */
class ProjectUserRoleUsernameFilter extends BaseFilter implements FilterInterface
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
            ->join(User::TABLE, 'id', 'user_id')
            ->ilike(User::TABLE.'.username', $this->value.'%');

        return $this;
    }
}
