<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;
use Kanboard\Model\ProjectActivity;

/**
 * Filter activity events by taskId
 *
 * @package filter
 * @author  Frederic Guillot
 */
class ProjectActivityTaskIdFilter extends BaseFilter implements FilterInterface
{
    /**
     * Get search attribute
     *
     * @access public
     * @return string[]
     */
    public function getAttributes()
    {
        return array('task_id');
    }

    /**
     * Apply filter
     *
     * @access public
     * @return FilterInterface
     */
    public function apply()
    {
        $this->query->eq(ProjectActivity::TABLE.'.task_id', $this->value);
        return $this;
    }
}
