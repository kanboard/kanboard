<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;
use Kanboard\Model\TaskModel;

/**
 * Filter tasks by project ids
 *
 * @package filter
 * @author  Frederic Guillot
 */
class TaskProjectsFilter extends BaseFilter implements FilterInterface
{
    /**
     * Get search attribute
     *
     * @access public
     * @return string[]
     */
    public function getAttributes()
    {
        return array('projects');
    }

    /**
     * Apply filter
     *
     * @access public
     * @return FilterInterface
     */
    public function apply()
    {
        if (empty($this->value)) {
            $this->query->eq(TaskModel::TABLE.'.project_id', 0);
        } else {
            $this->query->in(TaskModel::TABLE.'.project_id', $this->value);
        }

        return $this;
    }
}
