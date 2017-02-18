<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\SwimlaneModel;
use Kanboard\Model\TaskModel;

/**
 * Filter tasks by swimlane
 *
 * @package filter
 * @author  Frederic Guillot
 */
class TaskSwimlaneFilter extends BaseFilter implements FilterInterface
{
    /**
     * Get search attribute
     *
     * @access public
     * @return string[]
     */
    public function getAttributes()
    {
        return array('swimlane');
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
            $this->query->eq(TaskModel::TABLE.'.swimlane_id', $this->value);
        } else {
            $this->query->beginOr();
            $this->query->ilike(SwimlaneModel::TABLE.'.name', $this->value);
            $this->query->closeOr();
        }

        return $this;
    }
}
