<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;
use Kanboard\Model\ProjectActivityModel;

/**
 * Filter activity events by projectId
 *
 * @package filter
 * @author  Frederic Guillot
 */
class ProjectActivityProjectIdFilter extends BaseFilter implements FilterInterface
{
    /**
     * Get search attribute
     *
     * @access public
     * @return string[]
     */
    public function getAttributes()
    {
        return array('project_id');
    }

    /**
     * Apply filter
     *
     * @access public
     * @return FilterInterface
     */
    public function apply()
    {
        $this->query->eq(ProjectActivityModel::TABLE.'.project_id', $this->value);
        return $this;
    }
}
