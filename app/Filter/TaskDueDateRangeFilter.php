<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;
use Kanboard\Model\TaskModel;

/**
 * Filter tasks by due date range
 *
 * @package filter
 * @author  Frederic Guillot
 */
class TaskDueDateRangeFilter extends BaseFilter implements FilterInterface
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
        $this->query->gte(TaskModel::TABLE.'.date_due', is_numeric($this->value[0]) ? $this->value[0] : strtotime($this->value[0]));
        $this->query->lte(TaskModel::TABLE.'.date_due', is_numeric($this->value[1]) ? $this->value[1] : strtotime($this->value[1]));
        return $this;
    }
}
