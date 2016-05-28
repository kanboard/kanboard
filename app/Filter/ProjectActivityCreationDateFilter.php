<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;
use Kanboard\Model\ProjectActivityModel;

/**
 * Filter activity events by creation date
 *
 * @package filter
 * @author  Frederic Guillot
 */
class ProjectActivityCreationDateFilter extends BaseDateFilter implements FilterInterface
{
    /**
     * Get search attribute
     *
     * @access public
     * @return string[]
     */
    public function getAttributes()
    {
        return array('created');
    }

    /**
     * Apply filter
     *
     * @access public
     * @return FilterInterface
     */
    public function apply()
    {
        $this->applyDateFilter(ProjectActivityModel::TABLE.'.date_creation');
        return $this;
    }
}
