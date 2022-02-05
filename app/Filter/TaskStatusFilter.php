<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;
use Kanboard\Model\TaskModel;

/**
 * Filter tasks by status
 *
 * @package filter
 * @author  Frederic Guillot
 */
class TaskStatusFilter extends BaseFilter implements FilterInterface
{
    /**
     * Get search attribute
     *
     * @access public
     * @return string[]
     */
    public function getAttributes()
    {
        return array('status');
    }

    /**
     * Apply filter
     *
     * @access public
     * @return FilterInterface
     */
    public function apply()
    {
        if ($this->value === 'open' || $this->value === 'closed') {
            $this->query->eq(TaskModel::TABLE.'.is_active', $this->value === 'open' ? TaskModel::STATUS_OPEN : TaskModel::STATUS_CLOSED);
        } elseif (is_int($this->value) || ctype_digit((string) $this->value)) {
            $this->query->eq(TaskModel::TABLE.'.is_active', $this->value);
        }

        return $this;
    }
}
