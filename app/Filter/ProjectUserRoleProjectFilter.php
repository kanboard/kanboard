<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;
use Kanboard\Model\ProjectUserRoleModel;

/**
 * Filter ProjectUserRole users by project
 *
 * @package filter
 * @author  Frederic Guillot
 */
class ProjectUserRoleProjectFilter extends BaseFilter implements FilterInterface
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
        $this->query->eq(ProjectUserRoleModel::TABLE.'.project_id', $this->value);
        return $this;
    }
}
