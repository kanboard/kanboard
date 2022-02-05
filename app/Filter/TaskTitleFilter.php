<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;
use Kanboard\Model\TaskModel;

/**
 * Filter tasks by title
 *
 * @package filter
 * @author  Frederic Guillot
 */
class TaskTitleFilter extends BaseFilter implements FilterInterface
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
        if (ctype_digit((string) $this->value) || (strlen($this->value) > 1 && $this->value[0] === '#' && ctype_digit(substr($this->value, 1)))) {
            $this->query->beginOr();
            $this->query->eq(TaskModel::TABLE.'.id', str_replace('#', '', $this->value));
            $this->query->ilike(TaskModel::TABLE.'.title', '%'.$this->value.'%');
            $this->query->closeOr();
        } else {
            $this->query->ilike(TaskModel::TABLE.'.title', '%'.$this->value.'%');
        }

        return $this;
    }
}
