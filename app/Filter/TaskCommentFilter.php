<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;
use Kanboard\Model\CommentModel;
use Kanboard\Model\TaskModel;
use PicoDb\Database;

/**
 * Filter tasks by comment
 *
 * @package filter
 * @author  Frederic Guillot
 */
class TaskCommentFilter extends BaseFilter implements FilterInterface
{
    /**
     * Database object
     *
     * @access private
     * @var Database
     */
    private $db;

    /**
     * Get search attribute
     *
     * @access public
     * @return string[]
     */
    public function getAttributes()
    {
        return array('comment');
    }

    /**
     * Set database object
     *
     * @access public
     * @param  Database $db
     * @return $this
     */
    public function setDatabase(Database $db)
    {
        $this->db = $db;
        return $this;
    }

    /**
     * Apply filter
     *
     * @access public
     * @return FilterInterface
     */
    public function apply()
    {
        $task_ids = $this->getTaskIdsWithGivenComment();

        if (empty($task_ids)) {
            $task_ids = array(-1);
        }

        $this->query->in(TaskModel::TABLE.'.id', $task_ids);

        return $this;
    }

    /**
     * Get task ids having this comment
     *
     * @access public
     * @return array
     */
    protected function getTaskIdsWithGivenComment()
    {
        return $this->db
            ->table(CommentModel::TABLE)
            ->ilike(CommentModel::TABLE.'.comment', '%'.$this->value.'%')
            ->findAllByColumn(CommentModel::TABLE.'.task_id');
    }
}
