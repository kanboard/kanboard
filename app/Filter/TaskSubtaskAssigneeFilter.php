<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;
use Kanboard\Model\SubtaskModel;
use Kanboard\Model\TaskModel;
use Kanboard\Model\UserModel;
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
        $subquery = $this->db->table(SubtaskModel::TABLE)
            ->columns(
                SubtaskModel::TABLE.'.user_id',
                SubtaskModel::TABLE.'.task_id',
                UserModel::TABLE.'.name',
                UserModel::TABLE.'.username'
            )
            ->join(UserModel::TABLE, 'id', 'user_id', SubtaskModel::TABLE)
            ->neq(SubtaskModel::TABLE.'.status', SubtaskModel::STATUS_DONE);

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
            $subquery->eq(SubtaskModel::TABLE.'.user_id', $this->value);
        } else {
            switch ($this->value) {
                case 'me':
                    $subquery->eq(SubtaskModel::TABLE.'.user_id', $this->currentUserId);
                    break;
                case 'nobody':
                    $subquery->eq(SubtaskModel::TABLE.'.user_id', 0);
                    break;
                default:
                    $subquery->beginOr();
                    $subquery->ilike(UserModel::TABLE.'.username', $this->value.'%');
                    $subquery->ilike(UserModel::TABLE.'.name', '%'.$this->value.'%');
                    $subquery->closeOr();
            }
        }

        return $subquery;
    }
}
