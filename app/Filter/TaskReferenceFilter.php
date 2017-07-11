<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;
use Kanboard\Model\TaskModel;

/**
 * Filter tasks by reference
 *
 * @package filter
 * @author  Frederic Guillot
 */
class TaskReferenceFilter extends BaseFilter implements FilterInterface
{
    /**
     * Get search attribute
     *
     * @access public
     * @return string[]
     */
    public function getAttributes()
    {
        return array('reference', 'ref');
    }

    /**
     * Apply filter
     *
     * @access public
     * @return FilterInterface
     */
    public function apply()
    {
        if (strpos($this->value, '*') >= 0) {
            $this->query->like(TaskModel::TABLE.'.reference', str_replace('*', '%', $this->value));
            return $this;
        }

        $this->query->eq(TaskModel::TABLE.'.reference', $this->value);
        return $this;
    }
}
