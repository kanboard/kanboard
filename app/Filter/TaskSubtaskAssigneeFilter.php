<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;
use Kanboard\Model\Subtask;
use Kanboard\Model\Task;
use Kanboard\Model\User;
use PicoDb\Database;
use PicoDb\Table;

/**
 * Filter tasks by subtasks assignee
 *
 * @package filter
 * @author  Frederic Guillot
 */
class TaskSubtaskAssigneeFilter extends BaseFilter implements FilterInterface
{
    /**
     * Database object
     *
     * @access private
     * @var Database
     */
    private $db;

    /**
     * Current user id
     *
     * @access private
     * @var int
     */
    private $currentUserId = 0;

    /**
     * Set current user id
     *
     * @access public
     * @param  integer $userId
     * @return TaskSubtaskAssigneeFilter
     */
    public function setCurrentUserId($userId)
    {
        $this->currentUserId = $userId;
        return $this;
    }

    /**
     * Set database object
     *
     * @access public
     * @param  Database $db
     * @return TaskSubtaskAssigneeFilter
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
        return array('subtask:assignee');
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
        $subquery = $this->db->table(Subtask::TABLE)
            ->columns(
                Subtask::TABLE.'.user_id',
                Subtask::TABLE.'.task_id',
                User::TABLE.'.name',
                User::TABLE.'.username'
            )
            ->join(User::TABLE, 'id', 'user_id', Subtask::TABLE)
            ->neq(Subtask::TABLE.'.status', Subtask::STATUS_DONE);

        return $this->applySubQueryFilter($subquery);
    }

    /**
     * Apply subquery filter
     *
     * @access protected
     * @param  Table $subquery
     * @return Table
     */
    protected function applySubQueryFilter(Table $subquery)
    {
        if (is_int($this->value) || ctype_digit($this->value)) {
            $subquery->eq(Subtask::TABLE.'.user_id', $this->value);
        } else {
            switch ($this->value) {
                case 'me':
                    $subquery->eq(Subtask::TABLE.'.user_id', $this->currentUserId);
                    break;
                case 'nobody':
                    $subquery->eq(Subtask::TABLE.'.user_id', 0);
                    break;
                default:
                    $subquery->beginOr();
                    $subquery->ilike(User::TABLE.'.username', $this->value.'%');
                    $subquery->ilike(User::TABLE.'.name', '%'.$this->value.'%');
                    $subquery->closeOr();
            }
        }

        return $subquery;
    }
}
