<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;
use Kanboard\Model\ProjectGroupRoleModel;

/**
 * Filter ProjectGroupRole users by project
 *
 * @package filter
 * @author  Frederic Guillot
 */
class ProjectGroupRoleProjectFilter extends BaseFilter implements FilterInterface
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
        $this->query->eq(ProjectGroupRoleModel::TABLE.'.project_id', $this->value);
        return $this;
    }
}
