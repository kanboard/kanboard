<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;
use Kanboard\Model\TaskModel;

/**
 * Class TaskIdSearchFilter
 * 
 * @package Kanboard\Filter
 * @author  Frederic Guillot
 */
class TaskStartsWithIdFilter extends BaseFilter implements FilterInterface
{
    /**
     * Get search attribute
     *
     * @access public
     * @return string[]
     */
    public function getAttributes()
    {
        return array('starts_with_id');
    }

    /**
     * Apply filter
     *
     * @access public
     * @return FilterInterface
     */
    public function apply()
    {
        $this->query->ilike('CAST('.TaskModel::TABLE.'.id AS CHAR(8))', $this->value.'%');
        return $this;
    }
}
