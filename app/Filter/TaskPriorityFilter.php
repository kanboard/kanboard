<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;
use Kanboard\Model\TaskModel;

/**
 * Class TaskPriorityFilter
 *
 * @package Kanboard\Filter
 * @author  Frederic Guillot
 */
class TaskPriorityFilter extends BaseComparisonFilter implements FilterInterface
{
    /**
     * Get search attribute
     *
     * @access public
     * @return string[]
     */
    public function getAttributes()
    {
        return array('priority');
    }

    /**
     * Apply filter
     *
     * @access public
     * @return FilterInterface
     */
    public function apply()
    {
        $this->applyComparisonFilter(TaskModel::TABLE.'.priority');
        return $this;
    }
}
