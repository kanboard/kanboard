<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;
use Kanboard\Model\Task;

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
            $this->query->eq(Task::TABLE.'.is_active', $this->value === 'open' ? Task::STATUS_OPEN : Task::STATUS_CLOSED);
        } else {
            $this->query->eq(Task::TABLE.'.is_active', $this->value);
        }

        return $this;
    }
}
