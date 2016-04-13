<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;
use Kanboard\Model\Project;
use Kanboard\Model\Swimlane;
use Kanboard\Model\Task;

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
            $this->query->eq(Task::TABLE.'.swimlane_id', $this->value);
        } elseif ($this->value === 'default') {
            $this->query->eq(Task::TABLE.'.swimlane_id', 0);
        } else {
            $this->query->beginOr();
            $this->query->ilike(Swimlane::TABLE.'.name', $this->value);
            $this->query->ilike(Project::TABLE.'.default_swimlane', $this->value);
            $this->query->closeOr();
        }

        return $this;
    }
}
