<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;
use Kanboard\Model\TaskModel;

/**
 * Class TaskScoreFilter
 *
 * @package Kanboard\Filter
 */
class TaskScoreFilter extends BaseComparisonFilter implements FilterInterface
{
    /**
     * Get search attribute
     *
     * @access public
     * @return string[]
     */
    public function getAttributes()
    {
        return array('score', 'complexity');
    }

    /**
     * Apply filter
     *
     * @access public
     * @return FilterInterface
     */
    public function apply()
    {
        $this->applyComparisonFilter(TaskModel::TABLE.'.score');
        return $this;
    }
}
