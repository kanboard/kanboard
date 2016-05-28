<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;
use Kanboard\Model\UserModel;

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
            ->join(UserModel::TABLE, 'id', 'user_id')
            ->ilike(UserModel::TABLE.'.username', $this->value.'%');

        return $this;
    }
}
