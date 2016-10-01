<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;
use Kanboard\Model\CommentModel;
use Kanboard\Model\TaskModel;

/**
 * Filter tasks by comment
 *
 * @package filter
 * @author  Frederic Guillot
 */
class TaskCommentFilter extends BaseFilter implements FilterInterface
{
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
     * Apply filter
     *
     * @access public
     * @return FilterInterface
     */
    public function apply()
    {
        $this->query->ilike(CommentModel::TABLE.'.comment', '%'.$this->value.'%');
        $this->query->join(CommentModel::TABLE, 'task_id', 'id', TaskModel::TABLE);

        return $this;
    }
}
