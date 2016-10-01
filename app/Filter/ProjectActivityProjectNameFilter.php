<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;
use Kanboard\Model\ProjectModel;

/**
 * Filter activity events by project name
 *
 * @package filter
 * @author  Frederic Guillot
 */
class ProjectActivityProjectNameFilter extends BaseFilter implements FilterInterface
{
    /**
     * Get search attribute
     *
     * @access public
     * @return string[]
     */
    public function getAttributes()
    {
        return array('project');
    }

    /**
     * Apply filter
     *
     * @access public
     * @return FilterInterface
     */
    public function apply()
    {
        $this->query->ilike(ProjectModel::TABLE.'.name', '%'.$this->value.'%');
        return $this;
    }
}
