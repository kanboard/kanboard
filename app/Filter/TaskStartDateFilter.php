<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;
use Kanboard\Model\TaskModel;

/**
 * Filter tasks by start date
 *
 * @package filter
 * @author  Frederic Guillot
 */
class TaskStartDateFilter extends BaseDateFilter implements FilterInterface
{
    /**
     * Get search attribute
     *
     * @access public
     * @return string[]
     */
    public function getAttributes()
    {
        return array('started');
    }

    /**
     * Apply filter
     *
     * @access public
     * @return FilterInterface
     */
    public function apply()
    {
        $this->applyDateFilter(TaskModel::TABLE.'.date_started');
        return $this;
    }
}
