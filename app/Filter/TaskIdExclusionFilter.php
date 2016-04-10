<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;
use Kanboard\Model\Task;

/**
 * Exclude task ids
 *
 * @package filter
 * @author  Frederic Guillot
 */
class TaskIdExclusionFilter extends BaseFilter implements FilterInterface
{
    /**
     * Get search attribute
     *
     * @access public
     * @return string[]
     */
    public function getAttributes()
    {
        return array('exclude');
    }

    /**
     * Apply filter
     *
     * @access public
     * @return FilterInterface
     */
    public function apply()
    {
        $this->query->notin(Task::TABLE.'.id', $this->value);
        return $this;
    }
}
