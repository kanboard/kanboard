<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;
use Kanboard\Model\TaskModel;

/**
 * Filter tasks by due date
 *
 * @package filter
 * @author  Frederic Guillot
 */
class TaskDueDateFilter extends BaseDateFilter implements FilterInterface
{
    /**
     * Get search attribute
     *
     * @access public
     * @return string[]
     */
    public function getAttributes()
    {
        return array('due');
    }

    /**
     * Apply filter
     *
     * @access public
     * @return FilterInterface
     */
    public function apply()
    {
        $this->query->neq(TaskModel::TABLE.'.date_due', 0);
        $this->query->notNull(TaskModel::TABLE.'.date_due');
        $this->applyDateFilter(TaskModel::TABLE.'.date_due');

        return $this;
    }
}
