<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;
use Kanboard\Model\TaskModel;

/**
 * Filter tasks by creation date
 *
 * @package filter
 * @author  Frederic Guillot
 */
class TaskCreationDateFilter extends BaseDateFilter implements FilterInterface
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
        $this->applyDateFilter(TaskModel::TABLE.'.date_creation');
        return $this;
    }
}
