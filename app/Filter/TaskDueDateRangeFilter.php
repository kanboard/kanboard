<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;
use Kanboard\Model\TaskModel;

/**
 * Filter tasks by due date range
 *
 * @package filter
 * @author  Frederic Guillot
 */
class TaskDueDateRangeFilter extends BaseFilter implements FilterInterface
{
    private $startColumn;

    public function __construct($value = null, $startColumn = 'date_started')
    {
        parent::__construct($value);

        if (! in_array($startColumn, array('date_creation', 'date_started'), true)) {
            throw new \InvalidArgumentException('Invalid start date column');
        }

        $this->startColumn = $startColumn;
    }

    /**
     * Get search attribute
     *
     * @access public
     * @return string[]
     */
    public function getAttributes()
    {
        return array();
    }

    /**
     * Apply filter
     *
     * @access public
     * @return FilterInterface
     */
    public function apply()
    {
        $this->query->beginOr();
        $this->query->isNull(TaskModel::TABLE.'.'.$this->startColumn);
        $this->query->eq(TaskModel::TABLE.'.'.$this->startColumn, 0);
        $this->query->closeOr();

        $this->query->gte(TaskModel::TABLE.'.date_due', is_numeric($this->value[0]) ? $this->value[0] : strtotime($this->value[0]));
        $this->query->lte(TaskModel::TABLE.'.date_due', is_numeric($this->value[1]) ? $this->value[1] : strtotime($this->value[1]));
        return $this;
    }
}
