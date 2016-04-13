<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;
use Kanboard\Model\Link;
use Kanboard\Model\Task;
use Kanboard\Model\TaskLink;
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
            $this->query->in(Task::TABLE.'.id', $task_ids);
        } else {
            $this->query->eq(Task::TABLE.'.id', 0); // No match
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
        return $this->db->table(TaskLink::TABLE)
            ->columns(
                TaskLink::TABLE.'.task_id',
                Link::TABLE.'.label'
            )
            ->join(Link::TABLE, 'id', 'link_id', TaskLink::TABLE)
            ->ilike(Link::TABLE.'.label', $this->value);
    }
}
