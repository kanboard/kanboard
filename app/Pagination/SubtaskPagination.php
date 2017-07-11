<?php

namespace Kanboard\Pagination;

use Kanboard\Core\Base;
use Kanboard\Core\Paginator;
use Kanboard\Model\TaskModel;

/**
 * Class SubtaskPagination
 *
 * @package Kanboard\Pagination
 * @author  Frederic Guillot
 */
class SubtaskPagination extends Base
{
    /**
     * Get dashboard pagination
     *
     * @access public
     * @param  integer $userId
     * @return Paginator
     */
    public function getDashboardPaginator($userId)
    {
        return $this->paginator
            ->setUrl('DashboardController', 'subtasks', array('user_id' => $userId))
            ->setMax(50)
            ->setOrder(TaskModel::TABLE.'.priority')
            ->setDirection('DESC')
            ->setFormatter($this->taskListSubtaskAssigneeFormatter->withUserId($userId)->withoutEmptyTasks())
            ->setQuery($this->taskFinderModel->getUserQuery($userId))
            ->calculate();
    }
}
