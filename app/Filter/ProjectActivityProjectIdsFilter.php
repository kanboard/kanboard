<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;
use Kanboard\Model\ProjectActivityModel;

/**
 * Filter activity events by projectIds
 *
 * @package filter
 * @author  Frederic Guillot
 */
class ProjectActivityProjectIdsFilter extends BaseFilter implements FilterInterface
{
    /**
     * Get search attribute
     *
     * @access public
     * @return string[]
     */
    public function getAttributes()
    {
        return array('projects');
    }

    /**
     * Apply filter
     *
     * @access public
     * @return FilterInterface
     */
    public function apply()
    {
        if (empty($this->value)) {
            $this->query->eq(ProjectActivityModel::TABLE.'.project_id', 0);
        } else {
            $this->query->in(ProjectActivityModel::TABLE.'.project_id', $this->value);
        }

        return $this;
    }
}
