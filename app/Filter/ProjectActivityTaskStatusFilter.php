<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;
use Kanboard\Model\Task;

/**
 * Filter activity events by task status
 *
 * @package filter
 * @author  Frederic Guillot
 */
class ProjectActivityTaskStatusFilter extends BaseFilter implements FilterInterface
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
        if ($this->value === 'open') {
            $this->query->eq(Task::TABLE.'.is_active', Task::STATUS_OPEN);
        } elseif ($this->value === 'closed') {
            $this->query->eq(Task::TABLE.'.is_active', Task::STATUS_CLOSED);
        }

        return $this;
    }
}
