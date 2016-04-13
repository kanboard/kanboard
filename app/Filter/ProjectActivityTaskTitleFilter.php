<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;

/**
 * Filter activity events by task title
 *
 * @package filter
 * @author  Frederic Guillot
 */
class ProjectActivityTaskTitleFilter extends TaskTitleFilter implements FilterInterface
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
}
