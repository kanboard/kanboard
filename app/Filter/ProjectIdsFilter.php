<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;
use Kanboard\Model\ProjectModel;

/**
 * Filter project by ids
 *
 * @package filter
 * @author  Frederic Guillot
 */
class ProjectIdsFilter extends BaseFilter implements FilterInterface
{
    /**
     * Get search attribute
     *
     * @access public
     * @return string[]
     */
    public function getAttributes()
    {
        return array('project_ids');
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
            $this->query->eq(ProjectModel::TABLE.'.id', 0);
        } else {
            $this->query->in(ProjectModel::TABLE.'.id', $this->value);
        }

        return $this;
    }
}
