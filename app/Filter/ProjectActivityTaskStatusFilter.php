<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;
use Kanboard\Model\TaskModel;

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
            $this->query->eq(TaskModel::TABLE.'.is_active', TaskModel::STATUS_OPEN);
        } elseif ($this->value === 'closed') {
            $this->query->eq(TaskModel::TABLE.'.is_active', TaskModel::STATUS_CLOSED);
        }

        return $this;
    }
}
