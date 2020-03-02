<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;
use Kanboard\Model\TaskModel;

/**
 * Filter tasks by creator
 *
 * @package filter
 * @author  Frederic Guillot
 */
class TaskCreatorFilter extends BaseFilter implements FilterInterface
{
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
     * @return TaskAssigneeFilter
     */
    public function setCurrentUserId($userId)
    {
        $this->currentUserId = $userId;
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
        return array('creator');
    }

    /**
     * Apply filter
     *
     * @access public
     * @return string
     */
    public function apply()
    {
        if (is_int($this->value) || ctype_digit($this->value)) {
            $this->query->eq(TaskModel::TABLE.'.creator_id', $this->value);
        } else {
            switch ($this->value) {
                case 'me':
                    $this->query->eq(TaskModel::TABLE.'.creator_id', $this->currentUserId);
                    break;
                case 'nobody':
                    $this->query->eq(TaskModel::TABLE.'.creator_id', 0);
                    break;
                case 'anybody':
                    $this->query->gt(TaskModel::TABLE.'.creator_id', 0);
                    break;
                default:
                    $this->query->beginOr();
                    $this->query->ilike('uc.username', '%'.$this->value.'%');
                    $this->query->ilike('uc.name', '%'.$this->value.'%');
                    $this->query->closeOr();
            }
        }
    }
}
