<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;
use Kanboard\Model\LinkModel;
use Kanboard\Model\TaskLinkModel;
use Kanboard\Model\TaskModel;
use PicoDb\Database;
use PicoDb\Table;

/**
 * Filter tasks that are children of a given task
 *
 * Usage: childof:123, childof:#123 or childof:"task title"
 *
 * A task A is considered a child of task B when task_has_links contains
 * a row (task_id=A, opposite_task_id=B, link_id=<"is a child of">).
 *
 * @package filter
 */
class TaskChildOfFilter extends BaseFilter implements FilterInterface
{
    /**
     * Label of the default "is a child of" link
     *
     * @var string
     */
    const CHILD_OF_LABEL = 'is a child of';

    /**
     * Database object
     *
     * @access private
     * @var Database
     */
    private $db;

    /**
     * Set database object
     *
     * @access public
     * @param  Database $db
     * @return TaskChildOfFilter
     */
    public function setDatabase(Database $db)
    {
        $this->db = $db;
        return $this;
    }

    /**
     * Get search attribute
     *
     * @access public
     * @return string[]
     */
    public function getAttributes()
    {
        return array('childof');
    }

    /**
     * Apply filter
     *
     * @access public
     * @return FilterInterface
     */
    public function apply()
    {
        $this->query->inSubquery(TaskModel::TABLE.'.id', $this->getChildrenSubQuery());
        return $this;
    }

    /**
     * Get subquery returning the ids of all children of the given task(s)
     *
     * @access protected
     * @return Table
     */
    protected function getChildrenSubQuery()
    {
        return $this->db->table(TaskLinkModel::TABLE)
            ->columns(TaskLinkModel::TABLE.'.task_id')
            ->join(LinkModel::TABLE, 'id', 'link_id', TaskLinkModel::TABLE)
            ->eq(LinkModel::TABLE.'.label', self::CHILD_OF_LABEL)
            ->inSubquery(TaskLinkModel::TABLE.'.opposite_task_id', $this->getParentsSubQuery());
    }

    /**
     * Get subquery returning the ids of the parent task(s) matching the search value
     *
     * Reuses TaskTitleFilter so the accepted syntax matches the search box
     * (#123, 123, "title"). Kept as a subquery instead of an IN (...) list
     * so broad values cannot exceed the database placeholder limit.
     *
     * @access protected
     * @return Table
     */
    protected function getParentsSubQuery()
    {
        $query = $this->db->table(TaskModel::TABLE)->columns(TaskModel::TABLE.'.id');
        TaskTitleFilter::getInstance($this->value)->withQuery($query)->apply();
        return $query;
    }
}
