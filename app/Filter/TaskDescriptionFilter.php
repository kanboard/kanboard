<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;
use Kanboard\Model\TaskModel;

/**
 * Filter tasks by description
 *
 * @package filter
 * @author  Frederic Guillot
 */
class TaskDescriptionFilter extends BaseFilter implements FilterInterface
{
    /**
     * Get search attribute
     *
     * @access public
     * @return string[]
     */
    public function getAttributes()
    {
        return array('description', 'desc');
    }

    /**
     * Apply filter
     *
     * @access public
     * @return FilterInterface
     */
    public function apply()
    {
        $this->query->ilike(TaskModel::TABLE.'.description', '%'.$this->value.'%');
        return $this;
    }
}
