<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;
use Kanboard\Model\CategoryModel;
use Kanboard\Model\TaskModel;

/**
 * Filter tasks by category
 *
 * @package filter
 * @author  Frederic Guillot
 */
class TaskCategoryFilter extends BaseFilter implements FilterInterface
{
    /**
     * Get search attribute
     *
     * @access public
     * @return string[]
     */
    public function getAttributes()
    {
        return array('category');
    }

    /**
     * Apply filter
     *
     * @access public
     * @return FilterInterface
     */
    public function apply()
    {
        if (is_int($this->value) || ctype_digit((string) $this->value)) {
            $this->query->beginOr();
            $this->query->eq(TaskModel::TABLE.'.category_id', $this->value);
            $this->query->eq(CategoryModel::TABLE.'.name', $this->value);
            $this->query->closeOr();
        } elseif ($this->value === 'none') {
            $this->query->eq(TaskModel::TABLE.'.category_id', 0);
        } else {
            $this->query->eq(CategoryModel::TABLE.'.name', $this->value);
        }

        return $this;
    }
}
