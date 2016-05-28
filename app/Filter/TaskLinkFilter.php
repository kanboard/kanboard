<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;
use Kanboard\Model\LinkModel;
use Kanboard\Model\TaskModel;
use Kanboard\Model\TaskLinkModel;
use PicoDb\Database;
use PicoDb\Table;

/**
 * Filter tasks by link name
 *
 * @package filter
 * @author  Frederic Guillot
 */
class TaskLinkFilter extends BaseFilter implements FilterInterface
{
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
     * @return TaskLinkFilter
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
        return array('link');
    }

    /**
     * Apply filter
     *
     * @access public
     * @return string
     */
    public function apply()
    {
        $task_ids = $this->getSubQuery()->findAllByColumn('task_id');

        if (! empty($task_ids)) {
            $this->query->in(TaskModel::TABLE.'.id', $task_ids);
        } else {
            $this->query->eq(TaskModel::TABLE.'.id', 0); // No match
        }
    }

    /**
     * Get subquery
     *
     * @access protected
     * @return Table
     */
    protected function getSubQuery()
    {
        return $this->db->table(TaskLinkModel::TABLE)
            ->columns(
                TaskLinkModel::TABLE.'.task_id',
                LinkModel::TABLE.'.label'
            )
            ->join(LinkModel::TABLE, 'id', 'link_id', TaskLinkModel::TABLE)
            ->ilike(LinkModel::TABLE.'.label', $this->value);
    }
}
