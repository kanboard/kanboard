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
 * Usage: childof:123 or childof:"task title"
 *
 * A task A is considered a child of task B when task_has_links contains
 * a row (task_id=A, opposite_task_id=B, link_id=<"is a child of">).
 *
 * @package filter
 * @author  simon
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
        $this->query->inSubquery(TaskModel::TABLE.'.id', $this->getSubQuery());
        return $this;
    }

    /**
     * Get subquery returning the ids of all children of the given task(s)
     *
     * @access protected
     * @return Table
     */
    protected function getSubQuery()
    {
        return $this->db->table(TaskLinkModel::TABLE)
            ->columns(TaskLinkModel::TABLE.'.task_id')
            ->join(LinkModel::TABLE, 'id', 'link_id', TaskLinkModel::TABLE)
            ->ilike(LinkModel::TABLE.'.label', self::CHILD_OF_LABEL)
            ->in(TaskLinkModel::TABLE.'.opposite_task_id', $this->resolveParentIds());
    }

    /**
     * Resolve the search value to parent task ids
     *
     * Numeric values are treated as task ids, anything else as a task title
     * (case-insensitive partial match, like the title filter). Returns
     * array(0) when nothing matches so the filter yields no results.
     *
     * @access private
     * @return array
     */
    private function resolveParentIds()
    {
        $value = trim($this->value);

        if ($value === '') {
            return array(0);
        }

        if (ctype_digit($value)) {
            return array((int) $value);
        }

        $ids = $this->db->table(TaskModel::TABLE)
            ->ilike(TaskModel::TABLE.'.title', '%'.$value.'%')
            ->findAllByColumn('id');

        return ! empty($ids) ? $ids : array(0);
    }
}
