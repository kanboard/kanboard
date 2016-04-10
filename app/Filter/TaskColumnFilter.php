<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;
use Kanboard\Model\Column;
use Kanboard\Model\Task;

/**
 * Filter tasks by column
 *
 * @package filter
 * @author  Frederic Guillot
 */
class TaskColumnFilter extends BaseFilter implements FilterInterface
{
    /**
     * Get search attribute
     *
     * @access public
     * @return string[]
     */
    public function getAttributes()
    {
        return array('column');
    }

    /**
     * Apply filter
     *
     * @access public
     * @return FilterInterface
     */
    public function apply()
    {
        if (is_int($this->value) || ctype_digit($this->value)) {
            $this->query->eq(Task::TABLE.'.column_id', $this->value);
        } else {
            $this->query->eq(Column::TABLE.'.title', $this->value);
        }

        return $this;
    }
}
