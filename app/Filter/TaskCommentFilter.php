<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;
use Kanboard\Model\Comment;
use Kanboard\Model\Task;

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
        $this->query->ilike(Comment::TABLE.'.comment', '%'.$this->value.'%');
        $this->query->join(Comment::TABLE, 'task_id', 'id', Task::TABLE);

        return $this;
    }
}
