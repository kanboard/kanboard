<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;
use Kanboard\Model\Task;

/**
 * Filter activity events by task title
 *
 * @package filter
 * @author  Frederic Guillot
 */
class ProjectActivityTaskTitleFilter extends BaseFilter implements FilterInterface
{
    /**
     * Get search attribute
     *
     * @access public
     * @return string[]
     */
    public function getAttributes()
    {
        return array('title');
    }

    /**
     * Apply filter
     *
     * @access public
     * @return FilterInterface
     */
    public function apply()
    {
        $this->query->ilike(Task::TABLE.'.title', '%'.$this->value.'%');
        return $this;
    }
}
