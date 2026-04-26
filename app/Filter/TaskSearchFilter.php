<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;
use Kanboard\Model\CommentModel;
use Kanboard\Model\TaskModel;
use PicoDb\Database;

/**
 * Filter tasks by matching text across task fields
 *
 * @package filter
 * @author  Frederic Guillot
 */
class TaskSearchFilter extends BaseFilter implements FilterInterface
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
        return array('search');
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
        $this->query->beginOr();

        if (ctype_digit((string) $this->value) || (strlen($this->value) > 1 && $this->value[0] === '#' && ctype_digit(substr($this->value, 1)))) {
            $this->query->eq(TaskModel::TABLE.'.id', str_replace('#', '', $this->value));
        }

        $this->query->ilike(TaskModel::TABLE.'.title', '%'.$this->value.'%');
        $this->query->ilike(TaskModel::TABLE.'.description', '%'.$this->value.'%');
        $this->query->inSubquery(TaskModel::TABLE.'.id', $this->getCommentSubQuery());
        $this->query->closeOr();

        return $this;
    }

    /**
     * Get task ids having matching comments
     *
     * @access protected
     * @return array
     */
    protected function getCommentSubQuery()
    {
        return $this->db
            ->table(CommentModel::TABLE)
            ->columns(CommentModel::TABLE.'.task_id')
            ->ilike(CommentModel::TABLE.'.comment', '%'.$this->value.'%');
    }
}
