<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;
use Kanboard\Model\Task;

/**
 * Filter tasks by id
 *
 * @package filter
 * @author  Frederic Guillot
 */
class TaskIdFilter extends BaseFilter implements FilterInterface
{
    /**
     * Get search attribute
     *
     * @access public
     * @return string[]
     */
    public function getAttributes()
    {
        return array('id');
    }

    /**
     * Apply filter
     *
     * @access public
     * @return FilterInterface
     */
    public function apply()
    {
        $this->query->eq(Task::TABLE.'.id', $this->value);
        return $this;
    }
}
