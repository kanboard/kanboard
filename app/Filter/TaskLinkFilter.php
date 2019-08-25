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
        $this->query->inSubquery(TaskModel::TABLE.'.id', $this->getSubQuery());
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
                TaskLinkModel::TABLE.'.task_id'
            )
            ->join(LinkModel::TABLE, 'id', 'link_id', TaskLinkModel::TABLE)
            ->ilike(LinkModel::TABLE.'.label', $this->value);
    }
}
